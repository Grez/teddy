<?php

namespace Game\Entities\User;

use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Nette;
use Teddy\Entities;
use Doctrine\ORM\Mapping as ORM;



/**
 * @ORM\Entity()
 */
class UserAgent extends \Teddy\Entities\User\UserAgent
{

	use Identifier;

}
