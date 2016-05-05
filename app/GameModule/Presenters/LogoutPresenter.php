<?php

namespace Teddy\GameModule\Presenters;

class LogoutPresenter extends \Game\GameModule\Presenters\BasePresenter
{

	public function actionDefault()
	{
		$this->getUser()->logout();
		$this->flashMessage('You have been signed out.');
		$this->redirect(':Index:Homepage:default');
	}

}
