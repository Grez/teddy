<?php

namespace Game\Entities\PM;

use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Doctrine\ORM\Mapping as ORM;



/**
 * @ORM\Entity
 */
class Message extends \Teddy\Entities\PM\Message
{

	use Identifier;

}
