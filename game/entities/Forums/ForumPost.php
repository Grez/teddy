<?php

namespace Game\Entities\Forums;

use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Doctrine\ORM\Mapping as ORM;



/**
 * @ORM\Entity
 * @ORM\Table(indexes={
 *   @ORM\Index(columns={"forum_id", "deleted_at", "created_at"})
 * })
 */
class ForumPost extends \Teddy\Entities\Forums\ForumPost
{

	use Identifier;

}
