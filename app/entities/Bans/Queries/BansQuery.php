<?php

namespace Teddy\Entities\Logs;

use Kdyby\Doctrine\QueryBuilder;
use Kdyby\Doctrine\QueryObject;
use Kdyby\Persistence\Queryable;
use Teddy\Entities\Bans\Ban;



class BansQuery extends QueryObject
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
	 * @param string $ip
	 * @return $this
	 */
	public function byIp($ip)
	{
		$long = ip2long($ip);

		$this->filter[] = function (QueryBuilder $qb) use ($long) {
			$qb->andWhere('b.rangeStart <= :rangeStart', $long);
			$qb->andWhere('b.rangeEnd >= :rangeEnd', $long);
		};
		return $this;
	}



	/**
	 * @return $this
	 */
	public function onlyTotal()
	{
		$this->filter[] = function (QueryBuilder $qb) {
			$qb->andWhere('b.type = :type')->setParameter('type', Ban::TOTAL);
		};
		return $this;
	}



	/**
	 * @return $this
	 */
	public function onlyGame()
	{
		$this->filter[] = function (QueryBuilder $qb) {
			$qb->andWhere('b.type IN (:type)')->setParameter('type', [Ban::TOTAL, Ban::GAME]);
		};
		return $this;
	}



	/**
	 * @return $this
	 */
	public function onlyRegistration()
	{
		$this->filter[] = function (QueryBuilder $qb) {
			$qb->andWhere('b.type IN (:type)')->setParameter('type', [Ban::TOTAL, Ban::REGISTRATION]);
		};
		return $this;
	}


	/**
	 * @return $this
	 */
	public function activeOnly()
	{
		$this->filter[] = function (QueryBuilder $qb) {
			$qb->andWhere('b.endsAt >= :endsAt')->setParameter('endsAt', new \DateTime());
		};
		return $this;
	}


	/**
	 * @return $this
	 */
	public function maxOneResult()
	{
		$this->filter[] = function (QueryBuilder $qb) {
			$qb->setMaxResults(1);
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
	 * @return QueryBuilder
	 */
	private function createBasicDql(Queryable $repository)
	{
		$qb = $repository->createQueryBuilder()
			->select('b')
			->from(Ban::class, 'b');

		foreach ($this->filter as $modifier) {
			$modifier($qb);
		}

		return $qb;
	}

}
