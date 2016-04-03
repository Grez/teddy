<?php

namespace Teddy\Entities\PM;

use Kdyby\Doctrine\EntityManager;
use Nette;
use Kdyby;
use Teddy\Entities\User\User;



class MessagesListener extends Nette\Object implements Kdyby\Events\Subscriber
{

	/**
	 * @var Messages
	 */
	protected $messageFacade;

	/**
	 * @var EntityManager
	 */
	protected $em;



	public function __construct(Messages $messageFacade, EntityManager $em)
	{
		$this->messageFacade = $messageFacade;
		$this->em = $em;
	}



	public function onNewMessage(Message $message)
	{
		$this->sendUnreadMessagesWS($message->getTo());
	}



	public function onReadMessage(Message $message)
	{
		$this->sendUnreadMessagesWS($message->getTo());
	}



	public function onUnreadMessage(Message $message)
	{
		$this->sendUnreadMessagesWS($message->getTo());
	}



	public function onDeleteMessage(Message $message, User $deletedBy)
	{
		$this->sendUnreadMessagesWS($deletedBy);
	}



	protected function sendUnreadMessagesWS(User $user)
	{
		$this->em->flush(); // we need flush because getUnreadMessagesCount() is asking db
		// blabla ve skeletonu
	}



	public function getSubscribedEvents()
	{
		return [
			'\Teddy\Entities\PM\Messages::onNewMessage',
			'\Teddy\Entities\PM\Messages::onReadMessage',
			'\Teddy\Entities\PM\Messages::onUnreadMessage',
			'\Teddy\Entities\PM\Messages::onDeleteMessage',
		];
	}

}
