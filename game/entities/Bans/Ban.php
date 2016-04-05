<?php

namespace Game\Entities\Bans;

use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Nette;
use Teddy\Entities;
use Doctrine\ORM\Mapping as ORM;



/**
 * @ORM\Entity()
 */
class Ban extends \Teddy\Entities\Bans\Ban
{

	use Identifier;

}
