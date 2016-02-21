<?php

namespace Teddy\Security;

use Nette;
use Teddy\User\UserDoesNotExistException;



class User extends Nette\Security\User
{

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
		$id = $this->getAuthenticator()->authenticate(func_get_args());
		$this->storage->setIdentity($id);
		$this->storage->setAuthenticated(TRUE);
		$this->onLoggedIn($this, $email);
	}



	/**
	 * @param int $userId
	 * @return void
	 */
	public function passwordLessLogin($userId)
	{
		/** @var \Teddy\Entities\User\User $user */
		$user = $this->users->find($userId);
		if (!$user) {
			throw new UserDoesNotExistException();
		}

		$this->logout(TRUE);
		$id = new Nette\Security\Identity($user->getId());
		$this->storage->setIdentity($id);
		$this->storage->setAuthenticated(TRUE);
	}

}
