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
	 * @return $this
	 */
	public function onlyActivated()
	{
		$this->filter[] = function (QueryBuilder $qb) {
			$qb->andWhere('u.activated = TRUE');
		};
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
			$qb->andWhere('u.lastActivity >= :activity')->setParameter('activity', $date);
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
			$qb->andWhere('u.lastActivity >= :activity')->setParameter('activity', $date);
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
	 * @param enum ASC|DESC $order
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
			$qb->addOrderBy('u.registered', $order);
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
			->select('u')->from(User::class, 'u');

		foreach ($this->filter as $modifier) {
			$modifier($qb);
		}

		return $qb;
	}

}
