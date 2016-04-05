<?php

namespace Game\Entities\User;

use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Nette;
use Teddy\Entities;
use Doctrine\ORM\Mapping as ORM;



/**
 * @ORM\Entity(readOnly=TRUE)
 */
class Login extends \Teddy\Entities\User\Login
{

	use Identifier;

}
