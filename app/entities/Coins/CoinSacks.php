<?php

namespace Teddy\Entities\Coins;

use Nette;
use Teddy\Entities;
use Kdyby\Doctrine\EntityManager;
use Teddy\Entities\User\User;



class CoinSacks extends Entities\Manager
{

	public function __construct(EntityManager $em)
	{
		parent::__construct($em);
		$this->repository = $this->em->getRepository(\Game\Entities\Coins\CoinSack::class);
	}



	/**
	 * @param $amount
	 * @param User $user
	 * @param \DateTime|NULL $expires
	 */
	public function addCoinSack($amount, User $user, \DateTime $expires = NULL)
	{
		$coinSack = new \Game\Entities\Coins\CoinSack($amount, $user, $expires);
		$user->addCoinSack($coinSack);
		$this->em->flush();
	}



	/**
	 * @param int $amount
	 * @param User $user
	 * @throws AlreadyExpired
	 * @throws NotEnoughRemaining
	 * @throws NotEnoughTotal
	 */
	public function useCoins($amount, User $user)
	{
		if ($user->getTotalUsableCoins() < $amount) {
			throw new NotEnoughTotal();
		}

		$coinSacks = $user->getCoinSacks();
		foreach ($coinSacks as $coinSack) {
			if ($amount > 0) {
				$usedCoins = min($amount, $coinSack->getRemaining());
				$coinSack->useCoins($usedCoins);
				$amount -= $usedCoins;
			}
		}

		$this->em->flush();
	}

}
