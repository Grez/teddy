<?php

namespace Game\Entities\Forums;

use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Doctrine\ORM\Mapping as ORM;



/**
 * @ORM\Entity
 */
class ForumPost extends \Teddy\Entities\Forums\ForumPost
{

	use Identifier;

}
