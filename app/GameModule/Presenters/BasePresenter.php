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
	protected $player;

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
			$this->warningFlashMessage('You are not logged in');
			$this->redirect(':Index:Homepage:default');
		}

		if ($this->getUser()->getEntity()) {
			setcookie('teddy.userId', $this->getUser()->getEntity()->getId(), time() + 86400);
			setcookie('teddy.apiKey', $this->getUser()->getEntity()->getApiKey(), time() + 86400);
		}

		$this->player = $this->users->find($user->id);
		$this->template->user = $this->player;
	}



	/**
	 * Log user has done sth
	 * Maybe disable for ajax?
	 */
	public function afterRender()
	{
		parent::afterRender();
		$this->player->setLastActivityAt($this->timeProvider->getDateTime());
		$this->em->flush();
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
