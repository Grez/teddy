<?php

namespace Teddy\Entities\User;

use Nette;
use Kdyby;
use Teddy\Entities\Bans\Bans;



class LoginListener extends Nette\Object implements Kdyby\Events\Subscriber
{

	/** @var Logins */
	protected $logins;

	/** @var Users */
	protected $users;

	/** @var Bans */
	protected $bans;



	public function __construct(Logins $logins, Users $users, Bans $bans)
	{
		$this->logins = $logins;
		$this->users = $users;
		$this->bans = $bans;
	}



	public function onLoggedIn($identity, $login)
	{
		$user = $this->users->find($identity->id);
		$this->logins->log($user, $login);
	}



	public function onWrongPassword($login)
	{
		$this->logins->log(NULL, $login, Login::WRONG_PASSWORD);
		if ($this->logins->isTryingToHack($_SERVER['REMOTE_ADDR'])) {
			$this->bans->ban($_SERVER['REMOTE_ADDR'], 'Too many attempts to login', 1 / 24);
		}
	}



	public function onWrongLogin($login)
	{
		$this->logins->log(NULL, $login, Login::WRONG_LOGIN);
	}



	public function getSubscribedEvents()
	{
		return [
			'\Teddy\Security\User::onLoggedIn',
			'\Teddy\Entities\User\Users::onWrongLogin',
			'\Teddy\Entities\User\Users::onWrongPassword',
		];
	}

}
