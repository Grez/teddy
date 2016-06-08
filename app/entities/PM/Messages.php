<?php

namespace Teddy\Entities\PM;

use Nette;
use Teddy\Entities;
use Kdyby\Doctrine\EntityManager;
use Teddy\Entities\User\Player;


class Messages extends Entities\Manager
{

	/**
	 * @var array
	 */
	public $onNewMessage = [];

	/**
	 * @var array
	 */
	public $onDeleteMessage = [];

	/**
	 * @var array
	 */
	public $onReadMessage = [];

	/**
	 * @var array
	 */
	public $onUnreadMessage = [];



	public function __construct(EntityManager $em)
	{
		parent::__construct($em);
		$this->repository = $this->em->getRepository(\Game\Entities\PM\Message::class);
	}



	/**
	 * Sends new message
	 *
	 * @param Player $to
	 * @param Player $from
	 * @param string $subject
	 * @param string $text
	 * @param int $conversation
	 * @param int $type
	 * @return \Game\Entities\PM\Message
	 */
	public function createMessage(Player $to, Player $from, $subject = '', $text = '', $conversation = 0, $type = \Game\Entities\PM\Message::NORMAL_MSG)
	{
		$msg = new \Game\Entities\PM\Message($to, $from, $subject, $text, $type);
		if ($conversation > 0) {
			$msg->setConversation($this->find($conversation));
		}
		$this->em->persist($msg);
		$this->onNewMessage($msg);
		return $msg;
	}



	/**
	 * Marks message as read
	 *
	 * @param \Game\Entities\PM\Message $msg
	 */
	public function readMessage(\Game\Entities\PM\Message $msg)
	{
		$msg->markRead();
		$this->onReadMessage($msg);
	}



	/**
	 * Marks message as unread
	 *
	 * @param \Game\Entities\PM\Message $msg
	 */
	public function unreadMessage(\Game\Entities\PM\Message $msg)
	{
		$msg->markUnread();
		$this->onUnreadMessage($msg);
	}


	/**
	 * @param \Game\Entities\PM\Message $msg
	 * @param \Game\Entities\User\Player $user
	 * @throws \Teddy\User\InvalidArgumentException
	 */
	public function deleteBy(\Game\Entities\PM\Message $msg, \Game\Entities\User\Player $user)
	{
		$msg->deleteBy($user);
		$this->onDeleteMessage($msg, $user);
	}



	/**
	 * @param \Game\Entities\User\Player $user
	 * @return int
	 */
	public function getUnreadMessagesCount(\Game\Entities\User\Player $user)
	{
		$query = (new Entities\User\MessagesQuery())
			->onlyReceivedBy($user)
			->onlyNotDeletedByRecipient()
			->onlyUnread();
		return $this->repository->fetch($query)->getTotalCount();
	}

}
