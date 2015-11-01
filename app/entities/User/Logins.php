<?php

namespace Teddy\Entities\User;

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
	private $request;

	/**
	 * @var Nette\Http\Response
	 */
	private $response;



	public function __construct(EntityManager $em, Nette\Http\Request $request, Nette\Http\Response $response)
	{
		parent::__construct($em);
		$this->repository = $this->em->getRepository(Login::class);
		$this->request = $request;
		$this->response = $response;
	}



	/**
	 * @param User $user
	 * @param string $login
	 * @param int $error
	 * @return NULL
	 */
	public function log(User $user = NULL, $login = '', $error = 0)
	{
		$ua = $this->request->getHeader('User-Agent');
		$userAgent = $this->em->getRepository(UserAgent::class)->findOneBy(['userAgent' => $ua]) ?: new UserAgent($ua);
		$log = new Login($this->request, $userAgent, $login, $user, $error);
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
