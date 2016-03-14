<?php

namespace Teddy\Entities\PM;

use Nette;
use Teddy\Entities;
use Kdyby\Doctrine\EntityManager;
use Teddy\Entities\User\User;


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
		$this->repository = $this->em->getRepository(Message::class);
	}



	/**
	 * Sends new message
	 *
	 * @param User $to
	 * @param User $from
	 * @param string $subject
	 * @param string $text
	 * @param int $conversation
	 * @param int $type
	 * @return Message
	 */
	public function createMessage(User $to, User $from, $subject = '', $text = '', $conversation = 0, $type = Message::NORMAL_MSG)
	{
		$msg = new Message($to, $from, $subject, $text, $type);
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
	 * @param Message $msg
	 */
	public function readMessage(Message $msg)
	{
		$msg->markRead();
		$this->onReadMessage($msg);
	}



	/**
	 * Marks message as unread
	 *
	 * @param Message $msg
	 */
	public function unreadMessage(Message $msg)
	{
		$msg->markUnread();
		$this->onUnreadMessage($msg);
	}


	/**
	 * @param Message $msg
	 * @param User $user
	 * @throws \Teddy\User\InvalidArgumentException
	 */
	public function deleteBy(Message $msg, User $user)
	{
		$msg->deleteBy($user);
		$this->onDeleteMessage($msg, $user);
	}



	/**
	 * @param User $user
	 * @return int
	 */
	public function getUnreadMessagesCount(User $user)
	{
		$query = (new Entities\User\MessagesQuery())
			->onlyReceivedBy($user)
			->onlyNotDeletedByRecipient()
			->onlyUnread();
		return $this->repository->fetch($query)->getTotalCount();
	}

}
