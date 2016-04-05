<?php

namespace Game\Entities\Stats;

use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Doctrine\ORM\Mapping as ORM;



/**
 * @ORM\Entity
 */
class StatDetailed extends \Teddy\Entities\Stats\StatDetailed
{

	use Identifier;

}
