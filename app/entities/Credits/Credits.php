<?php

namespace Teddy\Entities\Credits;

use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Nette;
use Teddy\Entities;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Teddy\Entities\User\User;



/**
 * @ORM\Entity()
 */
class Credits extends \Kdyby\Doctrine\Entities\BaseEntity
{

	use Identifier;

	/**
	 * @ORM\Column(type="integer", nullable=FALSE)
	 * @var integer
	 */
	protected $amount = 0;

	/**
	 * @ORM\Column(type="integer", nullable=FALSE)
	 * @var integer
	 */
	protected $remaining = 0;

	/**
	 * @ORM\Column(type="datetime")
	 * @var \DateTime
	 * Generated in __construct()
	 */
	protected $added;

	/**
	 * @ORM\Column(type="datetime")
	 * @var \DateTime
	 */
	protected $expires;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	protected $description;

	/**
	 * @ORM\ManyToOne(targetEntity="\Teddy\Entities\User\User")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 * @var User
	 */
	protected $user;



	/**
	 * @param integer $amount
	 * @param User $user
	 * @param \DateTime|NULL $expires
	 */
	public function __construct($amount, User $user, \DateTime $expires = NULL)
	{
		$this->amount = $amount;
		$this->remaining = $amount;
		$this->added = new \DateTime();
		$this->expires = $expires ?: NULL;
	}



	/**
	 * @param integer $amount
	 */
	public function useAmount($amount)
	{
		if ($amount > $this->remaining) {
			throw new \InvalidArgumentException('You\'re trying to use too much credits');
		}

		if ($this->expires < new \DateTime()) {
			throw new CreditsAlreadyExpired($this->expires);
		}

		$this->remaining = $this->remaining - $amount;
	}

}
