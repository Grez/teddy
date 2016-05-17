<?php

namespace Teddy\Entities;

use Kdyby\Doctrine\Entities\BaseEntity;
use Kdyby\Doctrine\EntityManager;
use Kdyby\Doctrine\EntityRepository;
use Kdyby\Doctrine\QueryObject;
use Nette;
use Nette\Utils\ArrayHash;



abstract class Manager extends Nette\Object
{

	/**
	 * @var EntityManager
	 */
	protected $em;

	/**
	 * @var EntityRepository
	 */
	protected $repository;



	/**
	 * @param EntityManager $em
	 */
	public function __construct(EntityManager $em)
	{
		$this->em = $em;
	}



	/**
	 * @param array $criteria
	 * @param array $orderBy
	 * @param null|int $limit
	 * @param null|int $offset
	 * @return array
	 */
	public function findBy(array $criteria, array $orderBy = NULL, $limit = NULL, $offset = NULL)
	{
		return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
	}



	/**
	 * @param int $id
	 * @return null|object
	 */
	public function find($id)
	{
		return $this->repository->find($id);
	}



	/**
	 * @param QueryObject $query
	 * @return array|\Kdyby\Doctrine\ResultSet
	 */
	public function fetch(QueryObject $query)
	{
		return $this->repository->fetch($query);
	}



	/**
	 * Fetches all records like $key => $value pairs
	 *
	 * @param array $criteria parameter can be skipped
	 * @param string $value mandatory
	 * @param array $orderBy parameter can be skipped
	 * @param string $key optional
	 * @throws QueryException
	 * @return array
	 */
	public function findPairs($criteria, $value = NULL, $orderBy = [], $key = NULL)
	{
		return $this->repository->findPairs($criteria, $value, $orderBy, $key);
	}



	/**
	 * @param BaseEntity entity
	 * @param array values
	 */
	public function update(BaseEntity $entity, $values)
	{
		$this->setData($entity, $values);
		$this->save($entity);
		return $entity;
	}



	/**
	 * Persist entity and flush
	 *
	 * @param BaseEntity $entity
	 * @return BaseEntity
	 */
	public function save(BaseEntity $entity)
	{
		$this->em->persist($entity);
		$this->em->flush();
		return $entity;
	}



	/**
	 * Delete entity and flush
	 *
	 * @param BaseEntity entity
	 * @return BaseEntity
	 */
	public function delete(BaseEntity $entity)
	{
		$this->em->remove($entity);
		$this->em->flush();
		return $entity;
	}



	/**
	 * @param BaseEntity $entity
	 * @param array|ArrayHash $values
	 */
	protected function setData(BaseEntity $entity, $values)
	{
		foreach ($values as $key => $value) {
			if ($value instanceof ArrayHash || is_array($value)) {
				$this->setData($entity, $value);
			} else {
				$method = "set" . ucfirst($key);
				$entity->$method($value);
			}
		}
	}

}
