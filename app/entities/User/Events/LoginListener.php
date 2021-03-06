<?php

namespace Teddy\Entities\User;

use Nette;
use Kdyby;
use Teddy\Entities\Bans\Bans;



class LoginListener extends Nette\Object implements Kdyby\Events\Subscriber
{

	/**
	 * @var Logins
	 */
	protected $logins;

	/**
	 * @var Users
	 */
	protected $users;

	/**
	 * @var Bans
	 */
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



	public function onWrongPassword(User $user)
	{
		$this->logins->log($user, $user->getNick(), Login::ERROR_WRONG_PASSWORD);
		if ($this->logins->isTryingToHack($_SERVER['REMOTE_ADDR'])) {
			$this->bans->ban($_SERVER['REMOTE_ADDR'], 'Too many attempts to login', 1 / 24);
		}
	}



	public function onWrongNick($nick)
	{
		$this->logins->log(NULL, $nick, Login::ERROR_WRONG_NICK);
	}



	public function getSubscribedEvents()
	{
		return [
			'\Teddy\Security\UserContext::onLoggedIn',
			'\Teddy\Entities\User\Users::onWrongNick',
			'\Teddy\Entities\User\Users::onWrongPassword',
		];
	}

}
