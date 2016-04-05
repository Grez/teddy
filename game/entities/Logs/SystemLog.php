<?php

namespace Game\Entities\Logs;

use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Doctrine\ORM\Mapping as ORM;



/**
 * @ORM\Entity
 */
class SystemLog extends \Teddy\Entities\Logs\SystemLog
{

	use Identifier;

}
