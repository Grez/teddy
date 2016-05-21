<?php

namespace Game\Entities\User;

use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Doctrine\ORM\Mapping as ORM;



/**
 * @ORM\Entity
 * @ORM\Table(indexes={
 *   @ORM\Index(columns={"nick"}),
 *   @ORM\Index(columns={"email"}),
 *   @ORM\Index(columns={"deleted"})
 * })
 */
class User extends \Teddy\Entities\User\User
{

	use Identifier;

}
