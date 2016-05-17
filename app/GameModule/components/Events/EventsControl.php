<?php

namespace Teddy\GameModule\Components;

use Nette\Utils\ArrayHash;
use Nette\Utils\DateTime;
use Teddy;
use Teddy\Entities\PM\Messages;
use Teddy\Entities\User\Users;
use Teddy\Forms\Form;
use Kdyby\Doctrine\EntityManager;
use Nette\Application\UI\Control;
use Teddy\Security\User;


class EventsControl extends Control
{

	/**
	 * @var User
	 */
	protected $user;

	/**
	 * @var Messages
	 */
	protected $messagesFacade;

	/**
	 * @var int
	 */
	protected $unreadMessages = 0;

	/**
	 * @var int
	 */
	protected $notifications = 0;



	public function __construct(User $user, Messages $messagesFacade)
	{
		$this->user = $user;
		$this->messagesFacade = $messagesFacade;

		$this->unreadMessages = $this->messagesFacade->getUnreadMessagesCount($this->user->getEntity());
		$this->notifications = 0;
	}



	public function render()
	{
		$template = parent::createTemplate();
		$template->setFile(__DIR__ . '/events.latte');
		$template->unreadMessages = $this->unreadMessages;
		$template->notifications = $this->notifications;
		$template->events = $this->getNumberOfEvents();
		$template->render();
	}



	/**
	 * @return int
	 */
	protected function getNumberOfEvents()
	{
		return $this->unreadMessages + $this->notifications;
	}

}
