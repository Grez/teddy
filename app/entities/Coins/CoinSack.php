<?php

namespace Teddy\Entities\Coins;

use Nette;
use Teddy\Entities;
use Doctrine\ORM\Mapping as ORM;
use Teddy\Entities\User\Player;



/**
 * @ORM\MappedSuperclass()
 */
abstract class CoinSack extends \Kdyby\Doctrine\Entities\BaseEntity
{

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
	 * @ORM\ManyToOne(targetEntity="\Game\Entities\User\Player", inversedBy="coinSacks")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 * @var Player
	 */
	protected $user;



	/**
	 * @param int $coins
	 * @param Player $user
	 * @param \DateTime|NULL $expiresAt
	 */
	public function __construct($coins, Player $user, \DateTime $expiresAt = NULL)
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
