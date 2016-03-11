<?php

/**
 * @testCase
 */

namespace Teddy\Tests;

use Nette;
use Teddy\Entities\PM\Message;
use Teddy\Entities\PM\Messages;
use Teddy\Entities\User\User;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/../TestCase.php';



class MessagesTest extends TestCase
{

	/**
	 * @var User
	 */
	private $from;

	/**
	 * @var User
	 */
	private $to;

	/**
	 * @var Messages
	 */
	private $messages;

	/**
	 * @var Message
	 */
	private $msg;



	public function setUp()
	{
		parent::setUp();
		$this->from = \Mockery::mock(User::class);
		$this->from->shouldReceive('getId')->andReturn(1);
		$this->from->shouldReceive('getNick')->andReturn('From');

		$this->to = \Mockery::mock(User::class);
		$this->to->shouldReceive('getId')->andReturn(2);
		$this->to->shouldReceive('getNick')->andReturn('To');

		/** @var Messages messages */
		$this->messages = $this->getService(Messages::class);
		$this->msg = $this->messages->createMessage($this->to, $this->from, 'Subject', 'Text');
	}


	public function testReadability()
	{
		$mario = \Mockery::mock(User::class);
		$mario->shouldReceive('getId')->andReturn(42);

		Assert::true($this->msg->isReadableByUser($this->from));
		Assert::true($this->msg->isReadableByUser($this->to));
		Assert::false($this->msg->isReadableByUser($mario));

		$this->msg->deleteBy($this->from);
		Assert::false($this->msg->isReadableByUser($this->from));
		Assert::true($this->msg->isReadableByUser($this->to));

		$this->msg->deleteBy($this->to);
		Assert::false($this->msg->isReadableByUser($this->to));
	}

}

$test = new MessagesTest();
$test->run();
