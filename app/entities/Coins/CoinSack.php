<?php

namespace Teddy\Entities\Coins;

use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Nette;
use Teddy\Entities;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Teddy\Entities\User\User;



/**
 * @ORM\Entity()
 */
class CoinSack extends \Kdyby\Doctrine\Entities\BaseEntity
{

	use Identifier;

	/**
	 * @ORM\Column(type="integer", nullable=FALSE)
	 * @var int
	 */
	protected $coins = 0;

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
	 * @param int $coins
	 * @param User $user
	 * @param \DateTime|NULL $expiresAt
	 */
	public function __construct($coins, User $user, \DateTime $expiresAt = NULL)
	{
		$this->coins = $coins;
		$this->remaining = $coins;
		$this->user = $user;
		$this->expiresAt = $expiresAt;
		$this->addedAt = new \DateTime();
	}


	/**
	 * @param int $coins
	 * @throws AlreadyExpired
	 * @throws NotEnoughRemaining
	 */
	public function useCoins($coins)
	{
		if ($coins > $this->remaining) {
			throw new NotEnoughRemaining(sprintf('Remaining: %d, trying to use %d', $this->remaining, $coins));
		}

		if ($this->isExpired()) {
			throw new AlreadyExpired($this->expiresAt);
		}

		$this->remaining = $this->remaining - $coins;
	}


	/**
	 * @return bool
	 */
	public function isExpired()
	{
		return $this->expiresAt !== NULL && $this->expiresAt < new \DateTime();
	}


	/**
	 * @return int
	 */
	public function getRemaining()
	{
		return $this->remaining;
	}


	/**
	 * @return \DateTime
	 */
	public function getAddedAt()
	{
		return $this->addedAt;
	}


	/**
	 * @return \DateTime
	 */
	public function getExpiresAt()
	{
		return $this->expiresAt;
	}


	/**
	 * @return int
	 */
	public function getCoins()
	{
		return $this->coins;
	}


	/**
	 * @param \DateTime $expiresAt
	 * @return CoinSack
	 */
	public function setExpiresAt(\DateTime $expiresAt)
	{
		$this->expiresAt = $expiresAt;
		$this->user->orderCoinSacks();
		return $this;
	}

}
