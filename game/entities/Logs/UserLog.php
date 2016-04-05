<?php

namespace Game\Entities\Logs;

use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Doctrine\ORM\Mapping as ORM;



/**
 * @ORM\Entity
 */
class UserLog extends \Teddy\Entities\Logs\UserLog
{

	use Identifier;

}
