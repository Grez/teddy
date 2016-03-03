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
class Credit extends \Kdyby\Doctrine\Entities\BaseEntity
{

	use Identifier;

	/**
	 * @ORM\Column(type="integer", nullable=FALSE)
	 * @var int
	 */
	protected $amount = 0;

	/**
	 * @ORM\Column(type="integer", nullable=FALSE)
	 * @var int
	 */
	protected $remaining = 0;

	/**
	 * @ORM\Column(type="datetime")
	 * @var \DateTime
	 * Generated in __construct()
	 */
	protected $addedAt;

	/**
	 * @ORM\Column(type="datetime")
	 * @var \DateTime
	 */
	protected $expiresAt;

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
	 * @param int $amount
	 * @param User $user
	 * @param \DateTime|NULL $expiresAt
	 */
	public function __construct($amount, User $user, \DateTime $expiresAt = NULL)
	{
		$this->amount = $amount;
		$this->remaining = $amount;
		$this->addedAt = new \DateTime();
		$this->expiresAt = $expiresAt ?: NULL;
	}



	/**
	 * @param int $amount
	 */
	public function useAmount($amount)
	{
		if ($amount > $this->remaining) {
			throw new NotEnoughRemaining(sprintf('Remaining: %d, trying to use %d', $this->remaining, $amount));
		}

		if ($this->expiresAt < new \DateTime()) {
			throw new AlreadyExpired($this->expiresAt);
		}

		$this->remaining = $this->remaining - $amount;
	}

}
