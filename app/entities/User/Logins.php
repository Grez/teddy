<?php

namespace Teddy\Entities\User;

use Kdyby\Clock\IDateTimeProvider;
use Nette;
use Teddy\Entities;
use Kdyby\Doctrine\EntityManager;



class Logins extends Entities\Manager
{

	/** Max number of attempts in one hour before auto-ban */
	const ATTEMPTS = 15;

	/**
	 * @var Nette\Application\Request
	 */
	protected $request;

	/**
	 * @var Nette\Http\Response
	 */
	protected $response;

	/**
	 * @var IDateTimeProvider
	 */
	protected $dateTimeProvider;



	public function __construct(EntityManager $em, Nette\Http\Request $request, Nette\Http\Response $response, IDateTimeProvider $dateTimeProvider)
	{
		parent::__construct($em);
		$this->repository = $this->em->getRepository(\Game\Entities\User\Login::class);
		$this->request = $request;
		$this->response = $response;
		$this->dateTimeProvider = $dateTimeProvider;
	}



	/**
	 * @param \Game\Entities\User\User $user
	 * @param string $login
	 * @param int $error
	 * @return NULL
	 */
	public function log(\Game\Entities\User\User $user = NULL, $login = '', $error = 0)
	{
		if ($user instanceof User) {
			$user->setLastLoginAt($this->dateTimeProvider->getDateTime());
		}

		$ua = $this->request->getHeader('User-Agent');
		$ip = $this->request->getRemoteAddress();
		$cookie = $this->request->getCookie('login') ?: mt_rand(1e8, 9e8);
		$this->response->setCookie('login', $cookie, time() + 86400 * 365 * 42); // 42 years is answer to everything, including tracking cookies :D
		$fingerprint = $this->request->getCookie('fingerprint');
		$userAgent = $this->em->getRepository(\Game\Entities\User\UserAgent::class)->findOneBy(['userAgent' => $ua]) ?: new \Game\Entities\User\UserAgent($ua);
		$log = new \Game\Entities\User\Login($ip, $cookie, $fingerprint, $userAgent, $login, $user, $error);
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
			'error' => \Game\Entities\User\Login::ERROR_WRONG_PASSWORD,
		];
		$result = $this->findBy($criteria);
		return (count($result) >= self::ATTEMPTS);
	}
}
