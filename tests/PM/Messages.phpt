<?php

/**
 * @testCase
 */

namespace Teddy\Tests;

use Kdyby\Doctrine\ResultSet;
use Nette;
use Game\Entities\PM\Message;
use Teddy\Entities\PM\Messages;
use Game\Entities\User\User;
use Teddy\Entities\User\MessagesQuery;
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
		$this->from = new User('email@from.cz');
		$this->to = new User('email@to.cz');

		/** @var Messages messages */
		$this->messages = $this->getService(Messages::class);
		$this->getEm()->persist([$this->to, $this->from]);
		$this->msg = $this->messages->createMessage($this->to, $this->from, 'Subject', 'Text');
		$this->getEm()->persist($this->msg);
		$this->getEm()->flush();
	}



	public function testReadability()
	{
		$mario = new User('mario.luigi@quattro.formaggi.it');

		Assert::true($this->msg->isReadableByUser($this->from));
		Assert::true($this->msg->isReadableByUser($this->to));
		Assert::false($this->msg->isReadableByUser($mario));

		$this->msg->deleteBy($this->from);
		Assert::false($this->msg->isReadableByUser($this->from));
		Assert::true($this->msg->isReadableByUser($this->to));

		$this->msg->deleteBy($this->to);
		Assert::false($this->msg->isReadableByUser($this->to));
	}



	public function testMessageQuery()
	{
		$allMsgs = (new MessagesQuery());
		/** @var ResultSet $result */
		$result = $this->getEm()->getRepository(Message::class)->fetch($allMsgs);
		Assert::equal(1, count($result));
		Assert::equal($this->to, $result->toArray()[0]->getTo());

		$onlyReadableByTo = (new MessagesQuery())->onlyReadableBy($this->to);
		/** @var ResultSet $result */
		$result = $this->getEm()->getRepository(Message::class)->fetch($onlyReadableByTo);
		Assert::equal(1, count($result));
		Assert::equal($this->to, $result->toArray()[0]->getTo());

		$onlyReceivedBy = (new MessagesQuery())->onlyReceivedBy($this->from);
		/** @var ResultSet $result */
		$result = $this->getEm()->getRepository(Message::class)->fetch($onlyReceivedBy);
		Assert::equal(0, count($result));

		$combination = (new MessagesQuery())
			->onlyReceivedBy($this->to)
			->onlySentBy($this->from)
			->onlyReadableBy($this->to)
			->onlyReadableBy($this->from)
			->onlyUnread()
			->onlyNotDeletedByRecipient()
			->onlyNotDeletedBySender()
		/** @var ResultSet $result */;
		$result = $this->getEm()->getRepository(Message::class)->fetch($combination);
		Assert::equal(1, count($result));
	}

}

$test = new MessagesTest();
$test->run();
