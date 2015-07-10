<?php

namespace Teddy\Entities\User;

use Nette;
use Teddy\Entities;
use Nette\Security\Passwords;
use Kdyby\Doctrine\EntityManager;


class Users extends Entities\Manager implements Nette\Security\IAuthenticator
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


    public function __construct($salt, EntityManager $em)
    {
        parent::__construct($em);
        $this->salt = $salt;
        $this->repository = $this->em->getRepository(User::class);
    }

    /**
     * @param string $nick
     * @return User|false
     */
    public function getByNick($nick)
    {
        $data = $this->repository->findBy(array('nick' => $nick));
        return (count($data)) ? $data[0] : false;
    }

    /**
     * @param string $email
     * @return User|false
     */
    public function getByEmail($email)
    {
        $data = $this->repository->findBy(array('email' => $email));
        return (count($data)) ? $data[0] : false;
    }

    /**
     * @param int $token
     * @return User|false
     */
    public function getByToken($token)
    {
        $data = $this->repository->findBy(array('token' => $token, 'tokenExpiration >=' => new \DateTime()));
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
     * @return array
     */
    public function getAdmins()
    {
        $criteria = array(
            'admin' => true,
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
     * @param User $user
     */
    public function deleteAdminPermissions(User $user)
    {
        foreach ($user->adminPermissions as $permission) {
            $this->em->remove($permission);
        }
        $this->em->flush();
    }

    /**
     * @param User $user
     */
    public function deleteAdmin(User $user)
    {
        $this->deleteAdminPermissions($user);
        $user->setAdminDescription('');
        $user->setAdmin(false);
        $this->save($user);
    }

    /**
     * @param User $user
     * @param array $permissions
     */
    public function setAdminPermissions(User $user, $permissions)
    {
        // Delete old
        $this->deleteAdminPermissions($user);

        // Add new
        foreach ($permissions as $presenter) {
            $adminPermission = new AdminPermission();
            $adminPermission->setUser($user);
            $adminPermission->setPresenter($presenter);
            $user->addAdminPermission($adminPermission);
            $this->em->persist($adminPermission);
        }

        $this->em->flush();
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