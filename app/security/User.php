<?php

namespace Teddy\Security;

use Nette;
use Nette\Security\IAuthorizator;
use Nette\Security\IUserStorage;
use Teddy\Entities\User\Users;



class User extends Nette\Security\User
{

	/**
	 * @var \Game\Entities\User\User
	 */
	protected $entity;

	/**
	 * @var Users
	 */
	public $users;



	public function __construct(IUserStorage $storage, Users $users, IAuthorizator $authorizator = NULL)
	{
		parent::__construct($storage, $users, $authorizator);
		$this->users = $users;
	}


	/**
	 * Conducts the authentication process.
	 *
	 * @param string $email
	 * @param string $password
	 * @return void
	 * @throws Nette\Security\AuthenticationException if authentication was not successful
	 */
	public function login($email = NULL, $password = NULL)
	{
		$this->logout(TRUE);
		$identity = $this->getAuthenticator()->authenticate(func_get_args());
		$this->storage->setIdentity($identity);
		$this->storage->setAuthenticated(TRUE);
		$this->entity = $this->users->find($identity->getId());
		$this->onLoggedIn($this, $email);
	}



	/**
	 * @param int $userId
	 * @return void
	 */
	public function passwordLessLogin($userId)
	{
		$this->logout(TRUE);
		$identity = new Nette\Security\Identity($userId);
		$this->storage->setIdentity($identity);
		$this->storage->setAuthenticated(TRUE);
		$this->entity = $this->users->find($identity->getId());
	}


	/**
	 * @return \Game\Entities\User\User
	 */
	public function getEntity()
	{
		return $this->entity ?: $this->users->find($this->getId());
	}

}
