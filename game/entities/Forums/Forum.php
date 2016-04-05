<?php

namespace Game\Entities\Forums;

use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Doctrine\ORM\Mapping as ORM;



/**
 * @ORM\Entity
 */
class Forum extends \Teddy\Entities\Forums\Forum
{

	use Identifier;

}
