<?php

namespace Teddy\Model;

use Nette;
use Kdyby\Doctrine\EntityManager;


class ForumPosts extends Manager
{

    public function __construct(EntityManager $em)
    {
        parent::__construct($em);
        $this->repository = $this->em->getRepository(\Teddy\Model\ForumPost::class);
    }

}
