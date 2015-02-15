<?php

namespace App\Model;

use Kdyby\Doctrine\EntityDao;
use Kdyby\Doctrine\EntityManager;
use Nette;

class Manager extends Nette\Object
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

    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->dao->findBy($criteria, $orderBy, $limit, $offset);
    }

    public function find($id)
    {
        return $this->dao->find($id);
    }

}