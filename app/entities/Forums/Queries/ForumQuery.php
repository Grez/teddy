<?php

namespace Teddy\Entities\Forums;

use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Game\Entities\User\User;
use Kdyby\Persistence\Queryable;


class ForumsQuery extends \Kdyby\Doctrine\QueryObject
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
	 * @var User
	 */
	protected $user;



	public function __construct(User $user)
	{
		$this->user = $user;
	}



	/**
	 * @return $this
	 */
	public function withUnreadPostsCount()
	{
		$this->select[] = function (QueryBuilder $qb) {
			$qb->addSelect('COUNT(up.id) AS unread_posts_count, lv');
			$qb->leftJoin('f.lastVisits', 'lv');
			$qb->leftJoin('f.posts', 'up', Join::WITH, 'up.createdAt >= lv.lastVisitAt OR lv.lastVisitAt IS NULL');
			$qb->groupBy('f');
		};
		return $this;
	}



	/**
	 * @return $this
	 */
	public function withPosts()
	{
		$this->select[] = function (QueryBuilder $qb) {
			$qb->addSelect('p');
			$qb->leftJoin('f.posts', 'p');
			$qb->groupBy('f');
		};
		return $this;
	}



	/**
	 * @param \Game\Entities\Forums\Forum[] $forums
	 * @return $this
	 */
	public function onlyForums(array $forums)
	{
		$this->filter[] = function (QueryBuilder $qb) use ($forums) {
			$qb->andWhere('f IN (:forumsWhiteList)', $forums);
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
		return $this->createBasicDql($repository)->select('COUNT(f.id)');
	}



	/**
	 * @param Queryable $repository
	 * @return \Kdyby\Doctrine\QueryBuilder
	 */
	private function createBasicDql(Queryable $repository)
	{
		$qb = $repository->createQueryBuilder()
			->select('f')->from(\Game\Entities\Forums\Forum::class, 'f');

		foreach ($this->filter as $modifier) {
			$modifier($qb);
		}

		return $qb;
	}

}
