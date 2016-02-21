<?php

namespace Teddy\Entities\Logs;

use Kdyby\Doctrine\QueryBuilder;
use Kdyby\Doctrine\QueryObject;
use Kdyby\Persistence\Queryable;
use Teddy\Entities\User\User;



class UserLogsListQuery extends QueryObject
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
	 * @param int|User $user
	 * @return $this
	 */
	public function byUser($user)
	{
		$userId = $user instanceof User ? $user->getId() : $user;

		$this->filter[] = function (QueryBuilder $qb) use ($userId) {
			$qb->andWhere('u.id = :user')->setParameter('user', $userId);
		};
		return $this;
	}



	/**
	 * @param int $type
	 * @return $this
	 */
	public function byType($type)
	{
		$this->filter[] = function (QueryBuilder $qb) use ($type) {
			$qb->andWhere('l.type = :type')->setParameter('type', $type);
		};
		return $this;
	}



	/**
	 * @param string $order
	 * @return $this
	 */
	public function sortByDate($order = 'DESC')
	{
		$this->select[] = function (QueryBuilder $qb) use ($order) {
			$qb->addOrderBy('l.date', $order);
		};
		return $this;
	}



	/**
	 * @param Queryable $repository
	 * @return \Doctrine\ORM\Query|\Doctrine\ORM\QueryBuilder
	 */
	protected function doCreateQuery(Queryable $repository)
	{
		$qb = $this->createBasicDql($repository)
			->addSelect('partial u.{id, nick}');

		foreach ($this->select as $modifier) {
			$modifier($qb);
		}

		return $qb;
	}



	/**
	 * @param Queryable $repository
	 * @return QueryBuilder
	 */
	private function createBasicDql(Queryable $repository)
	{
		$qb = $repository->createQueryBuilder()
			->select('l')->from(UserLog::class, 'l')
			->innerJoin('l.user', 'u');

		foreach ($this->filter as $modifier) {
			$modifier($qb);
		}

		return $qb;
	}

}
