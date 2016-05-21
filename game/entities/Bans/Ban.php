<?php

namespace Game\Entities\Bans;

use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Nette;
use Teddy\Entities;
use Doctrine\ORM\Mapping as ORM;



/**
 * @ORM\Entity()
 * @ORM\Table(indexes={
 *   @ORM\Index(columns={"range_start", "range_end", "ends_at", "type"}),
 * })
 */
class Ban extends \Teddy\Entities\Bans\Ban
{

	use Identifier;

}
