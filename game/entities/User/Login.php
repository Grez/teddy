<?php

namespace Game\Entities\User;

use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Nette;
use Teddy\Entities;
use Doctrine\ORM\Mapping as ORM;



/**
 * @ORM\Entity(readOnly=TRUE)
 * @ORM\Table(indexes={
 *   @ORM\Index(columns={"ip"}),
 *   @ORM\Index(columns={"cookie"}),
 *   @ORM\Index(columns={"fingerprint"})
 * })
 */
class Login extends \Teddy\Entities\User\Login
{

	use Identifier;

}
