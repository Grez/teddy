<?php

namespace Teddy\AdminModule\Presenters;

use Nette;
use Teddy;
use Teddy\Entities\User\Player;
use Teddy\Entities\Logs\UserLogs;



class BasePresenter extends \Game\Presenters\BasePresenter
{

	/**
	 * @var Player
	 */
	protected $user;

	/**
	 * @var UserLogs
	 * @inject
	 */
	public $userLogs;

	/**
	 * @var array
	 */
	protected $presenters = [
		'Admin:Main' => 'Main',
		'Admin:Users' => 'Users',
		'Admin:Admins' => 'Admins',
		'Admin:Game' => [
			'name' => 'Game',
			'views' => [
				'stats' => 'Stats',
			]
		],
		'Admin:Antimulti' => [
			'name' => 'Antimulti',
			'views' => [
				'default' => 'Inspector',
				'newUsers' => 'New users',
				'bans' => 'Bans',
			],
		],
	];



	/**
	 * Is logged, is admin, is allowed?
	 */
	protected function startup()
	{
		parent::startup();

		$user = $this->getUser();
		if (!$user->isLoggedIn()) {
			$this->warningFlashMessage('You are not logged in');
			$this->redirect(':Index:Homepage:default');
		}

		$this->user = $this->users->find($user->id);
		if (!$this->user->isAdmin()) {
			$this->warningFlashMessage('You are not admin');
			$this->redirect(':Index:Homepage:default');
		}

		if ($this->presenter->getName() !== 'Admin:Main' && !$this->user->isAllowed($this->presenter->getName())) {
			$this->warningFlashMessage('You are not allowed here');
			$this->redirect(':Admin:Main:default');
		}

		$this->template->user = $this->user;
		$this->template->presenters = $this->presenters;

		$activePresenter = $this->presenters[$this->getPresenter()->getName()];
		$this->template->presenterName = is_array($activePresenter) ? $activePresenter['name'] : $activePresenter;
	}



	/**
	 * @return \WebLoader\Nette\CssLoader
	 */
	protected function createComponentCssAdmin()
	{
		return $this->webLoader->createCssLoader('admin');
	}



	/**
	 * @return \WebLoader\Nette\JavaScriptLoader
	 */
	protected function createComponentJsAdmin()
	{
		return $this->webLoader->createJavaScriptLoader('admin');
	}



	protected function getPresenters()
	{
		$presenters = [];
		foreach ($this->presenters as $presenter => $value) {
			$presenters[$presenter] = is_array($value) ? $value['name'] : $value;
		}
		return $presenters;
	}

}
