<?php

namespace Teddy\AdminModule\Presenters;

use Nette;
use Teddy;
use Teddy\Entities\User\User;
use Teddy\Entities\Logs\UserLogs;



class BasePresenter extends \Game\Presenters\BasePresenter
{

	/**
	 * @var UserLogs
	 * @inject
	 */
	public $userLogs;

	/**
	 * @var User
	 */
	protected $admin;

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
		$this->setMenuVariables();

		$this->template->user = $this->admin;
	}



	/**
	 * Checks if user is logged + is admin + can access current presenter
	 * if not redirects him to proper section (IndexModule:Homepage / default admin section)
	 */
	protected function checkPermissions()
	{
		$user = $this->getUser();
		if (!$user->isLoggedIn()) {
			$this->warningFlashMessage('You are not logged in');
			$this->redirect(':Index:Homepage:default');
		}

		/** @var User $admin */
		$admin = $this->users->find($user->id);
		if (!$admin->isAdmin() || !$admin->getAdminPermissions()->first()) {
			$this->warningFlashMessage('You are not admin');
			$this->redirect(':Index:Homepage:default');
		}

		if (!$admin->isAllowed($this->presenter->getName())) {
			$this->warningFlashMessage('You are not allowed here');
			$defaultPresenter = $admin->isAllowed(':Admin:Main:default') ? ':Admin:Main:default' : ':' . $admin->getAdminPermissions()->first()->getPresenter() . ':';
			$this->redirect($defaultPresenter);
		}

		$this->admin = $admin;
	}



	/**
	 * Necessary variables for Menu
	 */
	protected function setMenuVariables()
	{
		$activePresenter = $this->presenters[$this->getPresenter()->getName()];
		$this->template->presenters = $this->presenters;
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

}
