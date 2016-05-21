<?php

namespace Game\Entities\Stats;

use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Doctrine\ORM\Mapping as ORM;



/**
 * @ORM\Entity
 * @ORM\Table(uniqueConstraints={
 *   @ORM\UniqueConstraint(columns={"date", "time"})
 * })
 */
class StatDetailed extends \Teddy\Entities\Stats\StatDetailed
{

	use Identifier;

}
