<?php

namespace Game\Entities\Logs;

use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Doctrine\ORM\Mapping as ORM;



/**
 * @ORM\Entity
 * @ORM\Table(indexes={
 *   @ORM\Index(columns={"action"}),
 *   @ORM\Index(columns={"type"}),
 *   @ORM\Index(columns={"date"})
 * })
 */
class UserLog extends \Teddy\Entities\Logs\UserLog
{

	use Identifier;

}
