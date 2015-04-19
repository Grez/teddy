<?php

namespace Teddy\Model;

use Kdyby\Doctrine\Entities\BaseEntity;
use Kdyby\Doctrine\EntityDao;
use Kdyby\Doctrine\EntityManager;
use Nette;

abstract class Manager extends Nette\Object
{

    /** @var \Kdyby\Doctrine\EntityDao */
    protected $dao;

    /** @var \Kdyby\Doctrine\EntityManager @inject */
    protected $em;


    public function __construct(EntityDao $dao, EntityManager $em)
    {
        $this->dao = $dao;
        $this->em = $em;
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param null|int $limit
     * @param null|int $offset
     * @return array
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->dao->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param int $id
     * @return null|object
     */
    public function find($id)
    {
        return $this->dao->find($id);
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
     * @param BaseEntity entity
     */
    public function delete(BaseEntity $entity)
    {
        $this->em->remove($entity);
        $this->em->flush();
        return $entity;
    }
    
    /**
     * @param BaseEntity $entity
     * @param array|Nette\Utils\ArrayHash $values
     */
    protected function setData(BaseEntity $entity, $values)
    {
        foreach ($values as $key => $value) {
            if ($value instanceof Nette\Utils\ArrayHash || is_array($value)) {
                $this->setData($entity, $value);
            } else {
                $method = "set" . ucfirst($key);
                $entity->$method($value);
            }
        }
    }

}