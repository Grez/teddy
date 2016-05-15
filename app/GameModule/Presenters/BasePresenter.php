<?php

namespace Teddy\GameModule\Presenters;

use Teddy;
use Game\Entities\User\User;
use Nette;
use Game\GameModule\Components\IEventsControlFactory;



abstract class BasePresenter extends \Game\Presenters\BasePresenter
{

	/**
	 * @var User
	 */
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

		if ($this->getUser()->getEntity()) {
			setcookie('teddy.userId', $this->getUser()->getEntity()->getId(), time() + 86400);
			setcookie('teddy.apiKey', $this->getUser()->getEntity()->getApiKey(), time() + 86400);
		}

		$this->user = $this->users->find($user->id);
		$this->template->user = $this->user;
	}



	/**
	 * @return Teddy\GameModule\Components\EventsControl
	 */
	protected function createComponentEvents()
	{
		return $this->eventsFactory->create();
	}



	/**
	 * @return \WebLoader\Nette\JavaScriptLoader
	 */
	protected function createComponentJsGame()
	{
		return $this->webLoader->createJavaScriptLoader('game');
	}



	/**
	 * @return \WebLoader\Nette\CssLoader
	 */
	protected function createComponentCssGame()
	{
		return $this->webLoader->createCssLoader('game');
	}

}
