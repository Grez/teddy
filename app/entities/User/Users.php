<?php

namespace Teddy\Entities\User;

use Game\Entities\User\User;
use Nette;
use Nette\Utils\ArrayHash;
use Teddy\Entities;
use Nette\Security\Passwords;
use Kdyby\Doctrine\EntityManager;



/**
 * @method onWrongNick(string $nick)
 * @method onWrongPassword(\Game\Entities\User\User $user)
 */
class Users extends Entities\Manager implements Nette\Security\IAuthenticator
{

	/**
	 * int User is active if he logged in last ACTIVE days
	 */
	const ACTIVE = 30;

	/**
	 * int User is considered online if his last activity is in last ONLINE minutes
	 */
	const ONLINE = 15;

	/**
	 * @var array
	 */
	public $onWrongNick;

	/**
	 * @var array
	 */
	public $onWrongPassword;

	/**
	 * @var string; used for comparing passwords (config.local.neon)
	 */
	protected $salt = '';



	public function __construct($salt, EntityManager $em)
	{
		parent::__construct($em);
		$this->salt = $salt;
		$this->repository = $this->em->getRepository(\Game\Entities\User\User::class);
	}



	/**
	 * @param string $nick
	 * @return User|false
	 */
	public function getByNick($nick)
	{
		$data = $this->repository->findBy(['nick' => $nick]);
		return (count($data)) ? $data[0] : FALSE;
	}



	/**
	 * @param string $email
	 * @return User|false
	 */
	public function getByEmail($email)
	{
		$data = $this->repository->findBy(['email' => $email]);
		return (count($data)) ? $data[0] : FALSE;
	}



	/**
	 * @param int $token
	 * @return User|false
	 */
	public function getByToken($token)
	{
		$data = $this->repository->findBy(['token' => $token, 'tokenExpiresAt >=' => new \DateTime()]);
		return (count($data)) ? $data[0] : FALSE;
	}



	/**
	 * @param string $password
	 * @return User[]
	 */
	public function getByPassword($password)
	{
		$data = $this->repository->findBy(['password' => $password]);
		return $data;
	}



	/**
	 * @param int $id
	 * @param string $apikey
	 * @return User|NULL
	 */
	public function getByIdAndApikey($id, $apikey)
	{
		$user = $this->find($id);
		return $user && $user->getApikey() === $apikey ? $user : NULL;
	}



	/**
	 * @param ArrayHash $data
	 */
	public function register(ArrayHash $data)
	{
		$user = new User($data->email, $data->nick);
		$this->changePassword($user, $data['password']); // also makes flush
	}



	/**
	 * @param User $user
	 * @param string $password
	 * @return null
	 */
	public function changePassword(User $user, $password)
	{
		$options = $this->salt ? ['salt' => $this->salt] : [];
		$hashed = Passwords::hash($password, $options);
		$user->setPassword($hashed);
		$this->save($user);
	}



	/**
	 * @param User $user
	 */
	public function deleteAdminPermissions(User $user)
	{
		foreach ($user->getAdminPermissions() as $permission) {
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
		$user->setAdmin(FALSE);
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
			$adminPermission = new \Game\Entities\User\AdminPermission();
			$adminPermission->setUser($user);
			$adminPermission->setPresenter($presenter);
			$user->addAdminPermission($adminPermission);
			$this->em->persist($adminPermission);
		}

		$this->em->flush();
	}



	/**
	 * Performs an authentication.
	 *
	 * @return Nette\Security\Identity
	 * @throws Nette\Security\AuthenticationException
	 */
	public function authenticate(array $credentials)
	{
		list($nick, $password) = $credentials;
		$user = $this->getByNick($nick);

		if (!$user || $user->isDeleted()) {
			$this->onWrongNick($nick);
			throw new Nette\Security\AuthenticationException('The username is incorrect.', self::IDENTITY_NOT_FOUND);
		}

		if (Passwords::verify($password, $user->getPassword())) {
			return new Nette\Security\Identity($user->getId());
		}

		$this->onWrongPassword($user);
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
		return $user === FALSE || $user->getId() === $id;
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
