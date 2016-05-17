<?php

namespace Teddy\Entities\User;

use Doctrine\ORM\QueryBuilder;
use Kdyby\Persistence\Query;
use Kdyby\Persistence\Queryable;
use Nette\Utils\DateTime;



class UserListQuery extends \Kdyby\Doctrine\QueryObject
{

	/**
	 * @var array|\Closure[]
	 */
	private $filter = [];

	/**
	 * @var array|\Closure[]
	 */
	private $select = [];

	/**
	 * Blacklisted users
	 *
	 * @var int[]
	 */
	private $blackListedUserIds = [0];



	public function __construct($showDeleted = FALSE)
	{
		if (!$showDeleted) {
			$this->onlyUndeleted();
		}
	}



	/**
	 * @return $this
	 */
	public function onlyUndeleted()
	{
		$this->filter[] = function (QueryBuilder $qb) {
			$qb->andWhere('u.deleted = FALSE');
		};
		return $this;
	}



	/**
	 * @param \Game\Entities\User\User|int $user
	 * @return $this
	 */
	public function excludeUser($user)
	{
		$userId = $user instanceof \Game\Entities\User\User ? $userId = $user->getId() : $user;
		$this->blackListedUserIds[] = $userId;
		return $this;
	}



	/**
	 * @param int $days
	 * @return $this
	 */
	public function onlyActive($days = Users::ACTIVE)
	{
		$date = new DateTime();
		$date->setTimestamp(time() - ($days * 24 * 60 * 60));
		$this->filter[] = function (QueryBuilder $qb) use ($date) {
			$qb->andWhere('u.lastActivityAt >= :activity')->setParameter('activity', $date);
		};
		return $this;
	}



	/**
	 * @param int $minutes
	 * @return $this
	 */
	public function onlyOnline($minutes = Users::ONLINE)
	{
		$date = new DateTime();
		$date->setTimestamp(time() - ($minutes * 60));
		$this->filter[] = function (QueryBuilder $qb) use ($date) {
			$qb->andWhere('u.lastActivityAt >= :activity')->setParameter('activity', $date);
		};
		return $this;
	}



	/**
	 * @return $this
	 */
	public function onlyAdmins()
	{
		$this->filter[] = function (QueryBuilder $qb) {
			$qb->andWhere('u.admin = :admin')->setParameter('admin', TRUE);
		};
		return $this;
	}



	/**
	 * @param string - ASC|DESC $order
	 * @return $this
	 */
	public function orderByNick($order = 'ASC')
	{
		$this->select[] = function (QueryBuilder $qb) use ($order) {
			$qb->addOrderBy('u.nick', $order);
		};
		return $this;
	}



	/**
	 * @param string $order
	 * @return $this
	 */
	public function orderByRegistration($order = 'DESC')
	{
		$this->select[] = function (QueryBuilder $qb) use ($order) {
			$qb->addOrderBy('u.registeredAt', $order);
		};
		return $this;
	}



	/**
	 * @param Queryable $repository
	 * @return \Doctrine\ORM\Query|\Doctrine\ORM\QueryBuilder
	 */
	protected function doCreateQuery(Queryable $repository)
	{
		$qb = $this->createBasicDql($repository);

		foreach ($this->select as $modifier) {
			$modifier($qb);
		}

		return $qb;
	}



	/**
	 * @param Queryable $repository
	 * @return \Kdyby\Doctrine\QueryBuilder
	 */
	protected function doCreateCountQuery(Queryable $repository)
	{
		return $this->createBasicDql($repository)->select('COUNT(u.id)');
	}



	/**
	 * @param Queryable $repository
	 * @return \Kdyby\Doctrine\QueryBuilder
	 */
	private function createBasicDql(Queryable $repository)
	{
		$qb = $repository->createQueryBuilder()
			->select('u')->from(\Game\Entities\User\User::class, 'u')
			->andWhere('u.id NOT IN (:blackListedUserIds)', $this->blackListedUserIds);

		foreach ($this->filter as $modifier) {
			$modifier($qb);
		}

		return $qb;
	}

}
