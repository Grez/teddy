<?php

namespace Game\Entities\User;

use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Doctrine\ORM\Mapping as ORM;



/**
 * @ORM\Entity
 */
class User extends \Teddy\Entities\User\User
{

	use Identifier;

}
