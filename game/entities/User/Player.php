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
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"player" = "Player", "admin" = "Admin"})
 */
class Player extends \Teddy\Entities\User\Player
{

	use Identifier;

}
