<?php

namespace Teddy\Entities\Credits;

use Nette;
use Teddy\Entities;
use Kdyby\Doctrine\EntityManager;
use Teddy\Entities\User\User;
use Teddy\Entities\User\Users;



class Credits extends Entities\Manager
{

	public function __construct(EntityManager $em)
	{
		parent::__construct($em);
		$this->repository = $this->em->getRepository(Credit::class);
	}



	/**
	 * @param int $amount
	 * @param User $user
	 * @param \DateTime|NULL $expires
	 * @throws \Exception
	 */
	public function addCredit($amount, User $user, \DateTime $expires = NULL)
	{
		$credit = new Credit($amount, $user, $expires);
		$this->em->persist($credit);
		$this->em->flush();
	}



	public function useCredits($amount, User $user)
	{

	}



	public function getCredits(User $user, $onlyUsable = FALSE)
	{
		$criteria = ['user' => $user];

		if ($onlyUsable) {
			$criteria['expires >'] = new \DateTime();
			$criteria['remaining >'] = 0;
		}

		$this->repository->findBy([
			'user' => $user,
		]);
	}

}
