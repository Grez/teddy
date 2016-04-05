<?php

namespace Game\Entities\Stats;

use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Doctrine\ORM\Mapping as ORM;



/**
 * @ORM\Entity
 */
class StatDaily extends \Teddy\Entities\Stats\StatDaily
{

	use Identifier;

}
