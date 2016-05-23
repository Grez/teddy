<?php

namespace Game\Entities\Coins;

use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Nette;
use Teddy\Entities;
use Doctrine\ORM\Mapping as ORM;



/**
 * @ORM\Entity()
 */
class CoinSack extends \Teddy\Entities\Coins\CoinSack
{

	use Identifier;

}
