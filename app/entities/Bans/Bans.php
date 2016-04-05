<?php

namespace Teddy\Entities\Bans;

use Nette;
use Teddy\Entities;
use Kdyby\Doctrine\EntityManager;



class Bans extends Entities\Manager
{

	public function __construct(EntityManager $em)
	{
		parent::__construct($em);
		$this->repository = $this->em->getRepository(\Game\Entities\Bans\Ban::class);
	}



	/**
	 * @param string $ip
	 * @param string $reason
	 * @param int $days
	 * @param int $type
	 * @return NULL
	 */
	public function ban($ip, $reason = '', $days = 0, $type = Ban::GAME)
	{
		$endsAt = $days > 0 ? (new \DateTime())->modify('+ ' . $days . ' days') : NULL;
		$ban = new \Game\Entities\Bans\Ban($ip, $reason, $endsAt, $type);

		$this->em->persist($ban);
		$this->em->flush();
	}



	/**
	 * Checks whether IP is totally banned from website (403 error code)
	 *
	 * @param $ip
	 * @return Ban|NULL
	 */
	public function hasTotalBan($ip)
	{
		$query = (new Entities\Logs\BansQuery())
			->byIp($ip)
			->activeOnly()
			->onlyTotal()
			->maxOneResult();

		return $this->em->fetchOne($query);
	}



	/**
	 * Checks whether IP is banned from playing
	 *
	 * @param $ip
	 * @return Ban|NULL
	 */
	public function hasGameBan($ip)
	{
		$query = (new Entities\Logs\BansQuery())
			->byIp($ip)
			->activeOnly()
			->onlyGame()
			->maxOneResult();

		return $this->em->fetchOne($query);
	}



	/**
	 * Checks whether IP is banned from registration
	 *
	 * @param $ip
	 * @return Ban|NULL
	 */
	public function hasRegistrationBan($ip)
	{
		$query = (new Entities\Logs\BansQuery())
			->byIp($ip)
			->onlyRegistration()
			->maxOneResult();

		return $this->em->fetchOne($query);
	}

}
