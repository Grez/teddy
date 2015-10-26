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
		$userAgent = $this->em->getRepository(UserAgent::class)->findOneBy(['userAgent' => $_SERVER['HTTP_USER_AGENT']]);
		if (!$userAgent) {
			$userAgent = new UserAgent($_SERVER['HTTP_USER_AGENT']);
		}
		$log = new Login($userAgent);
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
		$criteria = [
			'ip' => $ip,
			'date >=' => $date,
			'error' => Login::ERROR_WRONG_PASSWORD,
		];
		$result = $this->findBy($criteria);
		return (count($result) >= self::ATTEMPTS);
	}
}
