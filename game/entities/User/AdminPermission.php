<?php

namespace Game\Entities\User;

use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Doctrine\ORM\Mapping as ORM;



/**
 * @ORM\Entity
 */
class AdminPermission extends \Teddy\Entities\User\AdminPermission
{

	use Identifier;

}
