<?php

/**
 * @testCase
 */

namespace Teddy\Tests;

use Kdyby\Clock\IDateTimeProvider;
use Nette;
use Game\Entities\Coins\CoinSack;
use Teddy\Entities\Coins\CoinSacks;
use Tester\Assert;
use Game\Entities\User\User;

require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/../TestCase.php';



class CoinSacksTest extends TestCase
{

	/**
	 * @var User
	 */
	private $user;

	/**
	 * @var CoinSack
	 */
	private $sack100;

	/**
	 * @var CoinSack
	 */
	private $sack150;

	/**
	 * @var CoinSacks
	 */
	private $facade;



	public function setUp()
	{
		parent::setUp();
		$timeProvider = $this->getService(IDateTimeProvider::class);
		$this->user = new User('mario.luigi@quattro.formaggi.it');
		$this->sack100 = new CoinSack(100, $this->user, $timeProvider->getDateTime()->modify('+ 10 min'));
		$this->user->addCoinSack($this->sack100);

		$this->sack150 = new CoinSack(150, $this->user, $timeProvider->getDateTime()->modify('+ 5 min'));
		$this->user->addCoinSack($this->sack150);

		$this->facade = $this->getService(CoinSacks::class);
	}



	public function testUsingCoins()
	{
		Assert::equal(250, $this->user->getTotalUsableCoins());
		$this->facade->useCoins(100, $this->user);
		Assert::equal(150, $this->user->getTotalUsableCoins());
	}



	public function testNotEnoughTotalCoins()
	{
		Assert::equal(250, $this->user->getTotalUsableCoins());
		Assert::exception(function () {
			$this->facade->useCoins(300, $this->user);
		}, 'Teddy\Entities\Coins\NotEnoughTotal');
		Assert::equal(250, $this->user->getTotalUsableCoins());
	}



	public function testOrdering()
	{
		Assert::equal(250, $this->user->getTotalUsableCoins());
		$this->facade->useCoins(5, $this->user);
		Assert::equal(245, $this->user->getTotalUsableCoins());
		Assert::equal(100, $this->sack100->getRemaining());
		Assert::equal(145, $this->sack150->getRemaining());

		$expiration = $this->sack150->getExpiresAt();
		$this->sack150->setExpiresAt($expiration->modify('+ 10 min'));
		$this->facade->useCoins(5, $this->user);
		Assert::equal(240, $this->user->getTotalUsableCoins());
		Assert::equal(95, $this->sack100->getRemaining());
		Assert::equal(145, $this->sack150->getRemaining());
	}

}

$test = new CoinSacksTest();
$test->run();
