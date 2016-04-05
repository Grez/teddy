<?php

namespace Teddy\Entities\Forums;

use Nette;
use Teddy\Entities;
use Kdyby\Doctrine\EntityManager;



class ForumPosts extends Entities\Manager
{

	public function __construct(EntityManager $em)
	{
		parent::__construct($em);
		$this->repository = $this->em->getRepository(\Game\Entities\Forums\ForumPost::class);
	}

}
