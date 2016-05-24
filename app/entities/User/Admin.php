<?php

namespace Teddy\Entities\User;

use Doctrine\ORM\Mapping as ORM;



class Admin extends \Game\Entities\User\User
{

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	protected $adminDescription = '';

}
