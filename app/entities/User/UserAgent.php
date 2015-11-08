<?php

namespace Teddy\Entities\User;

use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Nette;
use Teddy\Entities;
use Doctrine\ORM\Mapping as ORM;



/**
 * @ORM\Entity()
 */
class UserAgent extends \Kdyby\Doctrine\Entities\BaseEntity
{

	use Identifier;

	/**
	 * @ORM\Column(type="string", nullable=TRUE, unique=TRUE)
	 */
	protected $userAgent;



	public function __construct($userAgent)
	{
		$this->userAgent = $userAgent;
	}

}
