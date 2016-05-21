<?php

namespace Teddy\Entities\User;

use Nette;
use Teddy\Entities;
use Doctrine\ORM\Mapping as ORM;



/**
 * @ORM\MappedSuperclass()
 */
abstract class AdminPermission extends \Kdyby\Doctrine\Entities\BaseEntity
{

	/**
	 * @ORM\ManyToOne(targetEntity="\Game\Entities\User\User", inversedBy="adminPermissions")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 */
	protected $user;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $presenter;

}
