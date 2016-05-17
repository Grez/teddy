<?php

namespace Teddy\Entities\Stats;

use Nette;
use Teddy\Entities;
use Kdyby\Doctrine\EntityManager;
use Teddy\Entities\User\UserListQuery;



class StatDetailedManager extends Entities\Manager
{

	public function __construct(EntityManager $em)
	{
		parent::__construct($em);
		$this->repository = $this->em->getRepository(\Game\Entities\Stats\StatDetailed::class);
	}



	/**
	 * Saves statistics about players + server
	 *
	 * @return NULL
	 */
	public function create()
	{
		$userRepository = $this->em->getRepository(\Game\Entities\User\User::class);
		$userListQuery = new UserListQuery();
		$total = $userListQuery->count($userRepository);
		$active = $userListQuery->onlyActive()->count($userRepository);
		$online = $userListQuery->onlyOnline()->count($userRepository);

		$stat = new \Game\Entities\Stats\StatDetailed();
		$stat->setDate(new \DateTime());
		$stat->setTime(new \DateTime());
		$stat->setPlayersTotal($total);
		$stat->setPlayersActive($active);
		$stat->setPlayersOnline($online);

		if (function_exists('sys_getloadavg')) {
			$load = sys_getloadavg();
			$stat->setLoad1($load[0]);
			$stat->setLoad5($load[1]);
			$stat->setLoad15($load[2]);
		};

		$this->em->persist($stat);
		$this->em->flush();
	}



	/**
	 * @param \DateTimeInterface|null $from
	 * @param \DateTimeInterface|null $to
	 * @return array
	 */
	public function getStats($from = NULL, $to = NULL)
	{
		$criteria = [];

		if (!$from instanceof \DateTime) {
			$from = new \DateTime('now -1 month');
		}

		if (!$to instanceof \DateTime) {
			$to = new \DateTime('now');
		}

		$criteria['date >='] = $from;
		$criteria['date <='] = $to;

		return $this->repository->findBy($criteria, ['date' => 'DESC'], 60);
	}

}
