<?php

namespace Teddy\Entities\User;

use Doctrine\ORM\QueryBuilder;
use Kdyby\Persistence\Queryable;



class LoginListQuery extends \Kdyby\Doctrine\QueryObject
{

	/**
	 * @var array|\Closure[]
	 */
	protected $filter = [];

	/**
	 * @var array|\Closure[]
	 */
	protected $select = [];



	/**
	 * @param Player $user
	 * @return $this
	 */
	public function byUser(Player $user = NULL)
	{
		$this->filter[] = function (QueryBuilder $qb) use ($user) {
			$qb->andWhere('l.user = :user')->setParameter('user', $user);
		};
		return $this;
	}



	/**
	 * @param UserAgent $userAgent
	 * @return $this
	 */
	public function byUserAgent(UserAgent $userAgent = NULL)
	{
		$this->filter[] = function (QueryBuilder $qb) use ($userAgent) {
			$qb->andWhere('l.userAgent = :userAgent')->setParameter('userAgent', $userAgent);
		};
		return $this;
	}



	/**
	 * @param string $ip
	 * @return $this
	 */
	public function byIp($ip)
	{
		$this->filter[] = function (QueryBuilder $qb) use ($ip) {
			$qb->andWhere('l.ip = :ip')->setParameter('ip', $ip);
		};
		return $this;
	}



	/**
	 * @param integer $cookie
	 * @return $this
	 */
	public function byCookie($cookie)
	{
		$this->filter[] = function (QueryBuilder $qb) use ($cookie) {
			$qb->andWhere('l.cookie = :cookie')->setParameter('cookie', $cookie);
		};
		return $this;
	}



	/**
	 * @param $fingerprint
	 * @return $this
	 */
	public function byFingerprint($fingerprint)
	{
		$this->filter[] = function (QueryBuilder $qb) use ($fingerprint) {
			$qb->andWhere('l.fingerprint = :fingerprint')->setParameter('fingerprint', $fingerprint);
		};
		return $this;
	}



	/**
	 * @return $this
	 */
	public function onlySuccessful()
	{
		$this->filter[] = function (QueryBuilder $qb) {
			$qb->andWhere('l.error = 0');
		};
		return $this;
	}



	/**
	 * @return $this
	 */
	public function onlyUnsuccessful()
	{
		$this->filter[] = function (QueryBuilder $qb) {
			$qb->andWhere('l.error > 0');
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
		return $this->createBasicDql($repository)->select('COUNT(l.id)');
	}



	/**
	 * @param Queryable $repository
	 * @return \Kdyby\Doctrine\QueryBuilder
	 */
	private function createBasicDql(Queryable $repository)
	{
		$qb = $repository->createQueryBuilder()
			->select('l')
			->from(\Game\Entities\User\Login::class, 'l')
			->innerJoin('l.user', 'u')
			->setMaxResults(500)
			->addOrderBy('l.date', 'DESC');

		foreach ($this->filter as $modifier) {
			$modifier($qb);
		}

		return $qb;
	}

}
