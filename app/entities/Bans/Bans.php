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
		$this->repository = $this->em->getRepository(Ban::class);
	}



	/**
	 * @param string $ip
	 * @param string $reason
	 * @param int $days
	 * @param string $type
	 * @return NULL
	 */
	public function ban($ip, $reason = '', $days = 0, $type = Ban::GAME)
	{
		// Max && default ban = 10 years
		if ($days == 0 || $days >= 3650) {
			$days = 3650;
		}

		$ban = new Ban();
		$ban->setIp($ip);
		$ban->setReason($reason);
		$ban->setType($type);

		if ($days) {
			$until = new \Nette\Utils\DateTime();
			$until->setTimestamp(time() + $days * 86400);
			$ban->setUntil($until);
		}

		$this->em->persist($ban);
		$this->em->flush();
	}



	/**
	 * Returns all bans for IP
	 *
	 * @param string $ip
	 * @return array
	 */
	public function checkIp($ip)
	{
		$long = ip2long($ip);
		$query = $this->repository->createQueryBuilder('b')
			->where('b.start = :start', $long)
			->andWhere('b.end >= :end', $long)
			->andWhere('b.until IS NULL OR b.until >= :utill', new \DateTime())
			->getQuery();
		return $query->getResult();
	}



	/**
	 * Checks whether IP is totally banned from website (403 error code)
	 *
	 * @param $ip
	 * @return Ban|bool
	 */
	public function hasTotalBan($ip)
	{
		$bans = $this->checkIp($ip);
		if (is_array($bans)) {
			foreach ($bans as $ban) {
				if ($ban->getType() == Ban::TOTAL) {
					return $ban;
				}
			}
		}
		return FALSE;
	}



	/**
	 * Checks whether IP can login
	 *
	 * @param string $ip
	 * @return Ban|bool
	 */
	public function hasLoginBan($ip)
	{
		$bans = $this->checkIp($ip);
		if (is_array($bans)) {
			foreach ($bans as $ban) {
				if (in_array($ban->getType(), [Ban::TOTAL, Ban::GAME])) {
					return $ban;
				}
			}
		}
		return FALSE;
	}



	/**
	 * Checks whether IP can register
	 *
	 * @param string $ip
	 * @return Ban|bool
	 */
	public function hasRegistrationBan($ip)
	{
		$bans = $this->checkIp($ip);
		if (is_array($bans)) {
			foreach ($bans as $ban) {
				if (in_array($ban->getType(), [Ban::TOTAL, Ban::REGISTRATION, Ban::GAME])) {
					return $ban;
				}
			}
		}
		return FALSE;
	}



	public function getBans()
	{
		return $this->findBy([]);
	}

}
