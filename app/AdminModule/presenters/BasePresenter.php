<?php

namespace Teddy\AdminModule\Presenters;

use Nette;
use Teddy;
use Teddy\Entities\User\User;
use Teddy\Entities\Logs\UserLogs;



class BasePresenter extends \Game\Presenters\BasePresenter
{

	/**
	 * @var User
	 */
	protected $admin;

	/**
	 * @var UserLogs
	 * @inject
	 */
	public $userLogs;

	/**
	 * Used for generating menu
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



	protected function startup()
	{
		parent::startup();
		$this->checkPermissions();
		$this->template->user = $this->admin;

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



	/**
	 * Checks if user is logged + is admin + can access current presenter
	 * if not redirects to proper section (IndexModule:Homepage / AdminModule:Main)
	 */
	protected function checkPermissions()
	{
		$user = $this->getUser();
		if (!$user->isLoggedIn()) {
			$this->warningFlashMessage('You are not logged in');
			$this->redirect(':Index:Homepage:default');
		}

		$this->admin = $this->users->find($user->id);
		if (!$this->admin->isAdmin()) {
			$this->warningFlashMessage('You are not admin');
			$this->redirect(':Index:Homepage:default');
		}

		if ($this->presenter->getName() !== 'Admin:Main' && !$this->admin->isAllowed($this->presenter->getName())) {
			$this->warningFlashMessage('You are not allowed here');
			$this->redirect(':Admin:Main:default');
		}
	}

}
