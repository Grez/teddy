<?php

namespace Teddy\Entities\User;

use Nette;
use Teddy\Entities;
use Kdyby\Doctrine\EntityManager;



class Logins extends Entities\Manager
{

	/** Max number of attempts in one hour before auto-ban */
	const ATTEMPTS = 15;



	public function __construct(EntityManager $em)
	{
		parent::__construct($em);
		$this->repository = $this->em->getRepository(Login::class);
	}



	/**
	 * @param User $user
	 * @param string $login
	 * @param int $error
	 * @return NULL
	 */
	public function log(User $user = NULL, $login = '', $error = 0)
	{
		$log = new Login();
		$log->setError($error);

		if ($user) {
			$log->setUser($user);
		}

		if ($login) {
			$log->setLogin($login);
		}

		$this->em->persist($log);
		$this->em->flush();
	}



	/**
	 * Is someone trying to hack into another account?
	 * Tried wrong password >= self::ATTEMPT times in last two hours
	 *
	 * @param $ip
	 * @return bool
	 */
	public function isTryingToHack($ip)
	{
		$date = new \DateTime();
		$date->setTimestamp(time() - 3600);
		$criteria = array(
			'ip' => $ip,
			'date >=' => $date,
			'error' => Login::WRONG_PASSWORD,
		);
		$result = $this->findBy($criteria);
		return (count($result) >= self::ATTEMPTS);
	}
}
