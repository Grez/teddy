<?php

namespace Teddy\GameModule\Presenters;

use Teddy;
use Teddy\Entities\User\User;
use Nette;
use Teddy\GameModule\Components\IEventsControlFactory;



abstract class BasePresenter extends Teddy\Presenters\BasePresenter
{

	/** @var User */
	protected $user;

	/**
	 * @var IEventsControlFactory
	 * @inject
	 */
	public $eventsFactory;



	protected function startup()
	{
		parent::startup();

		$user = $this->getUser();
		if (!$user->isLoggedIn()) {
			$this->flashMessage(_('You are not logged in'), 'error');
			$this->redirect(':Index:Homepage:default');
		}

		$this->user = $this->users->find($user->id);
		$this->template->user = $this->user;
	}



	protected function createComponentEvents()
	{
		return $this->eventsFactory->create();
	}

}
