<?php

namespace Game\Entities\PM;

use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Doctrine\ORM\Mapping as ORM;



/**
 * @ORM\Entity
 * @ORM\Table(indexes={
 *   @ORM\Index(columns={"to_user_id", "from_user_id", "deleted_by_sender", "deleted_by_recipient"}),
 * })
 */
class Message extends \Teddy\Entities\PM\Message
{

	use Identifier;

}
