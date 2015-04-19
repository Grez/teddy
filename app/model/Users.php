<?php

namespace Teddy\Model;

use Nette;
use Nette\Security\Passwords;
use Kdyby\Doctrine\EntityDao;
use Kdyby\Doctrine\EntityManager;

class Users extends Manager implements Nette\Security\IAuthenticator
{

    /** int User is active if he logged in last ACTIVE days */
    const ACTIVE = 30;

    /** int User is considered online if his last activity is in last ONLINE minutes */
    const ONLINE = 15;

    /** @var array of function(string $login); Occurs when user uses wrong login */
    public $onWrongLogin;

    /** @var array of function(string $login); Occurs when user uses wrong password */
    public $onWrongPassword;

    /** @var string; used for comparing passwords (config.local.neon) */
    protected $salt = '';


    public function __construct($salt, EntityDao $dao, EntityManager $em)
    {
        parent::__construct($dao, $em);
        $this->salt = $salt;
    }

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

    /**
     * @param int $token
     * @return User|false
     */
    public function getByToken($token)
    {
        $data = $this->dao->findBy(array('token' => $token, 'tokenExpiration >=' => new \DateTime()));
        return (count($data)) ? $data[0] : false;
    }

    /**
     * Gets list of active players
     */
    public function getPlayersList()
    {
        $criteria = array(
            'active' => 'T',
        );
        return $this->findBy($criteria);
    }

    /**
     * @param \Nette\Utils\ArrayHash $values
     */
    public function register($data)
    {
        $password = Passwords::hash($data['password']);

        $user = new User();
        $user->setNick($data->nick);
        $user->setEmail($data->email);
        $user->setPassword($password);
        $this->save($user);
    }

    /**
     * @param User $user
     * @param string $password
     * @return null
     */
    public function changePassword(User $user, $password)
    {
        $options = ($this->salt != '') ? array('salt' => $this->salt) : array();
        $hashed = Passwords::hash($password, $options);
        $user->setPassword($hashed);
        $this->save($user);
    }

    /**
     * Performs an authentication.
     * @return Nette\Security\Identity
     * @throws Nette\Security\AuthenticationException
     */
    public function authenticate(array $credentials)
    {
        list($login, $password) = $credentials;
        $user = $this->getByNick($login);

        $byNick = $this->getByNick($login);
        $byEmail = $this->getByEmail($login);

        if (!$byNick && !$byEmail) {
            $this->onWrongLogin($login);
            throw new Nette\Security\AuthenticationException('The username is incorrect.', self::IDENTITY_NOT_FOUND);
        }

        if ($byNick) {
            if (Passwords::verify($password, $byNick->getPassword())) {
                return new Nette\Security\Identity($user->getId());
            }
        }

        if ($byEmail) {
            if (Passwords::verify($password, $user->getPassword())) {
                return new Nette\Security\Identity($user->getId());
            }
        }

        $this->onWrongPassword($login);
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
        return Passwords::verify($control->getValue(), $this->find($id)->getPassword());
    }

}