<?php

namespace App\Model;

use Nette;
use Nette\Security\Passwords;

class Users extends Manager implements Nette\Security\IAuthenticator
{

    /** int User is active if he logged in last ACTIVE days */
    const ACTIVE = 30;

    /** int User is considered online if his last activity is in last ONLINE minutes */
    const ONLINE = 15;


    /**
     * @param string $nick
     * @return User|false
     */
    public function getByNick($nick)
    {
        $data = $this->dao->findBy(array('nick' => $nick));
        return (count($data)) ? $data[0] : false;
    }

    /**
     * @param string $email
     * @return User|false
     */
    public function getByEmail($email)
    {
        $data = $this->dao->findBy(array('email' => $email));
        return (count($data)) ? $data[0] : false;
    }

    public function register($data)
    {
        $password = \Nette\Security\Passwords::hash($data['password']);

        $user = new User();
        $user->setNick($data->nick);
        $user->setEmail($data->email);
        $user->setPassword($password);

        $this->em->persist($user);
        $this->em->flush();
    }

    /**
     * Performs an authentication.
     * @param array(login, password) $credentials
     * @return Nette\Security\Identity
     * @throws Nette\Security\AuthenticationException
     */
    public function authenticate(array $credentials)
    {
        list($login, $password) = $credentials;
        $user = $this->getByNick($login);

        if ($user) {
            if (Passwords::verify($password, $user->getPassword())) {
                return new Nette\Security\Identity($user->getId());
            }
        } else {
            $user = $this->getByEmail($login);
            if(!$user) {
                throw new Nette\Security\AuthenticationException('The username is incorrect.', self::IDENTITY_NOT_FOUND);
            }

            if (Passwords::verify($password, $user->getPassword())) {
                return new Nette\Security\Identity($user->getId());
            }
        }

        throw new Nette\Security\AuthenticationException('The password is incorrect.', self::INVALID_CREDENTIAL);
    }

    /**
     * Returns number of ($active?) players
     * @param bool $onlyActive
     * @return int
     */
    public function getTotal($onlyActive = false)
    {
        $date = new \DateTime();
        $date->setTimestamp(time() - (self::ONLINE * 24 * 60 * 60));
        $resource = ($onlyActive) ? $this->dao->findAll() : $this->dao->findBy(array('lastLogin >=' => $date));
        return count($resource);
    }

    /**
     * Returns number of online players
     * @return int
     */
    public function getOnline()
    {
        $date = new \DateTime();
        $date->setTimestamp(time() - (self::ONLINE * 60));
        $resource = $this->dao->findBy(array('lastActivity >=' => $date));
        return count($resource);
    }

    /******************** Validators ********************/

    /***
     * @param Nette\Forms\Controls\TextInput $control
     * @param int $id
     * @return boolean
     */
    public function validateNick(Nette\Forms\Controls\TextInput $control, $id)
    {
        $id = (int) ($id instanceof Nette\Forms\Controls\BaseControl ? $id->getValue() : $id);
        $user = $this->getByNick($control->getValue());
        return $user === false || $user->getId() === $id;
    }

    /**
     * @param Nette\Forms\Controls\TextInput $control
     * @param int $id
     * @return boolean
     */
    public function validatePassword(Nette\Forms\Controls\TextInput $control, $id)
    {
        $password = $this->getUserPassword($id);
        return $password && $password->isEqual($control->getValue());
    }

}