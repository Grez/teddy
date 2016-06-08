<?php

/**
 * @testCase
 */

namespace Teddy\Tests;

use Kdyby\Doctrine\ResultSet;
use Nette;
use Game\Entities\PM\Message;
use Teddy\Entities\PM\Messages;
use Game\Entities\User\Player;
use Teddy\Entities\User\MessagesQuery;
use Teddy\Entities\User\UserListQuery;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/../TestCase.php';



class UserListQueryTest extends TestCase
{

	/**
	 * @var Player
	 */
	private $user1;

	/**
	 * @var Player
	 */
	private $user2;



	public function setUp()
	{
		parent::setUp();
		$this->user1 = new Player('mario@luigi.it');
		$this->user2 = new Player('quattro@formaggi.it');
		$this->user3 = new Player('pizza@plumber.it');
		$this->user3->setDeleted(TRUE);

		$this->getEm()->persist($this->user1, $this->user2, $this->user3);
		$this->getEm()->flush();
	}



	public function testMessageQuery()
	{
		$allUsers = (new UserListQuery());

		/** @var ResultSet $result */
		$result = $this->getEm()->getRepository(Message::class)->fetch($allUsers);
		Assert::equal(2, count($result));

		$excludedUsers = (new UserListQuery())->excludeUser($this->user2)->excludeUser($this->user3);
		/** @var ResultSet $result */
		$result = $this->getEm()->getRepository(Message::class)->fetch($excludedUsers);
		Assert::equal(1, count($result));
		Assert::equal($this->user1, $result->toArray()[0]);

		$withDeleted = (new UserListQuery(TRUE));
		/** @var ResultSet $result */
		$result = $this->getEm()->getRepository(Message::class)->fetch($withDeleted);
		Assert::equal(3, count($result));
	}

}

$test = new UserListQueryTest();
$test->run();
