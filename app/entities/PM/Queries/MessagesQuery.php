<?php

namespace Teddy\Entities\User;

use Doctrine\ORM\QueryBuilder;
use Kdyby\Persistence\Queryable;
use Teddy\Entities\PM\Message;


class MessagesQuery extends \Kdyby\Doctrine\QueryObject
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
	 * @param User $user
	 * @return $this
	 */
	public function onlyReadableBy(User $user)
	{
		$this->filter[] = function (QueryBuilder $qb) use ($user) {
			$qb->andWhere('m.from = :user OR m.to = :user')->setParameter('user', $user);
		};
		return $this;
	}



	/**
	 * @param User $user
	 * @return $this
	 */
	public function onlyReceivedBy(User $user)
	{
		$this->filter[] = function (QueryBuilder $qb) use ($user) {
			$qb->andWhere('m.to = :to')->setParameter('to', $user);
		};
		return $this;
	}



	/**
	 * @param User $user
	 * @return $this
	 */
	public function onlySentBy(User $user)
	{
		$this->filter[] = function (QueryBuilder $qb) use ($user) {
			$qb->andWhere('m.from = :from')->setParameter('from', $user);
		};
		return $this;
	}



	/**
	 * @return $this
	 */
	public function onlyNotDeletedByRecipient()
	{
		$this->filter[] = function (QueryBuilder $qb) {
			$qb->andWhere('m.deletedByRecipient = FALSE');
		};
		return $this;
	}



	/**
	 * @return $this
	 */
	public function onlyNotDeletedBySender()
	{
		$this->filter[] = function (QueryBuilder $qb) {
			$qb->andWhere('m.deletedBySender = FALSE');
		};
		return $this;
	}



	/**
	 * @param string $order
	 * @return $this
	 */
	public function orderBySentAt($order = 'DESC')
	{
		$this->select[] = function (QueryBuilder $qb) use ($order) {
			$qb->addOrderBy('m.sentAt', $order);
		};
		return $this;
	}



	/**
	 * @TODO: join frrm, to
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
		return $this->createBasicDql($repository)->select('COUNT(m.id)');
	}



	/**
	 * @param Queryable $repository
	 * @return \Kdyby\Doctrine\QueryBuilder
	 */
	private function createBasicDql(Queryable $repository)
	{
		$qb = $repository->createQueryBuilder()
			->select('m')->from(Message::class, 'm');

		foreach ($this->filter as $modifier) {
			$modifier($qb);
		}

		return $qb;
	}

}
