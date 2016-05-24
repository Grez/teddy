<?php

namespace Game\Entities\User;

use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Doctrine\ORM\Mapping as ORM;



/**
 * @ORM\Entity
 * @ORM\Table(name="player", indexes={
 *   @ORM\Index(columns={"nick"}),
 *   @ORM\Index(columns={"email"}),
 *   @ORM\Index(columns={"deleted"})
 * })
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"player" = "Player", "admin" = "Admin"})
 */
class User extends \Teddy\Entities\User\User
{

	use Identifier;

}
