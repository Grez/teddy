<?php

namespace Teddy\Entities\PM;

use Nette;
use Teddy\Entities;
use Kdyby\Doctrine\EntityManager;
use Teddy\Entities\User\User;



class Messages extends Entities\Manager
{

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
		return $msg;
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
