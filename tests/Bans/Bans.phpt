<?php

/**
 * @testCase
 */

namespace Teddy\Tests;

use Nette;
use Teddy\Entities\Bans\Ban;
use Teddy\Entities\Bans\Bans;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/../TestCase.php';



class BansTest extends TestCase
{

	/**
	 * @var Bans
	 */
	private $bans;

	/**
	 * @var Ban
	 */
	private $totalBan;

	/**
	 * @var Ban
	 */
	private $gameBan;

	/**
	 * @var Bans
	 */
	private $registrationBan;

	/**
	 * @var Ban
	 */
	private $rangeBan;



	public function setUp()
	{
		parent::setUp();
		$this->bans = $this->getService(Bans::class);
		$this->totalBan = new Ban('127.0.0.1', 'Total', NULL, Ban::TOTAL);
		$this->gameBan = new Ban('129.0.0.1', 'Game', NULL, Ban::GAME);
		$this->registrationBan = new Ban('125.0.0.1', 'Registration', NULL, Ban::REGISTRATION);
		$this->rangeBan = new Ban('100.0.0.*', 'Range');
		$this->getEm()->persist([$this->totalBan, $this->gameBan, $this->registrationBan, $this->rangeBan]);
		$this->getEm()->flush();
	}


	/**
	 * @todo: any site will send me 403
	 */
	public function testDifferentTypesOfBan()
	{
		$result = $this->bans->hasTotalBan('241.0.0.1');
		Assert::null($result);
		$result = $this->bans->hasTotalBan('127.0.0.1');
		Assert::same($this->totalBan, $result);

		// Test game bans
		$result = $this->bans->hasGameBan('129.0.0.1');
		Assert::same($this->gameBan, $result);
		$result = $this->bans->hasGameBan('127.0.0.1');
		Assert::same($this->totalBan, $result);

		// Test registration bans
		$result = $this->bans->hasRegistrationBan('125.0.0.1');
		Assert::same($this->registrationBan, $result);
		$result = $this->bans->hasRegistrationBan('127.0.0.1');
		Assert::same($this->totalBan, $result);

		// Test ban with range
		$result = $this->bans->hasGameBan('100.0.1.15');
		Assert::null($result);
		$result = $this->bans->hasGameBan('100.0.0.15');
		Assert::same($this->rangeBan, $result);
	}

}

$test = new BansTest();
$test->run();
