<?php

namespace Teddy\Entities\Forums;

use Doctrine\ORM\QueryBuilder;
use Kdyby\Persistence\Queryable;


class PostsQuery extends \Kdyby\Doctrine\QueryObject
{

	/**
	 * @var array|\Closure[]
	 */
	protected $filter = [];

	/**
	 * @var array|\Closure[]
	 */
	protected $select = [];



	public function __construct($showDeleted = FALSE)
	{
		if (!$showDeleted) {
			$this->onlyNotDeleted();
		}
	}



	/**
	 * @param \Game\Entities\Forums\Forum|int $forum
	 * @return $this
	 */
	public function onlyFromForum($forum)
	{
		$forumId = $forum instanceof \Game\Entities\Forums\Forum ? $forum->getId() : $forum;

		$this->filter[] = function (QueryBuilder $qb) use ($forumId) {
			$qb->andWhere('f.id = :forumId', $forumId);
		};
		return $this;
	}



	/**
	 * @return $this
	 */
	public function onlyNotDeleted()
	{
		$this->filter[] = function (QueryBuilder $qb) {
			$qb->andWhere('p.deletedAt IS NULL');
		};
		return $this;
	}



	/**
	 * @param string $order
	 * @return $this
	 */
	public function orderByCreatedAt($order = 'DESC')
	{
		$this->select[] = function (QueryBuilder $qb) use ($order) {
			$qb->addOrderBy('p.createdAt', $order);
		};
		return $this;
	}



	/**
	 * @return $this
	 */
	public function withUser()
	{
		$this->select[] = function (QueryBuilder $qb) {
			$qb->addSelect('u');
			$qb->innerJoin('p.author', 'u');
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
		return $this->createBasicDql($repository)->select('COUNT(p.id)');
	}



	/**
	 * @param Queryable $repository
	 * @return \Kdyby\Doctrine\QueryBuilder
	 */
	private function createBasicDql(Queryable $repository)
	{
		$qb = $repository->createQueryBuilder()
			->select('p')->from(\Game\Entities\Forums\ForumPost::class, 'p')
			->innerJoin('p.forum', 'f');

		foreach ($this->filter as $modifier) {
			$modifier($qb);
		}

		return $qb;
	}

}
