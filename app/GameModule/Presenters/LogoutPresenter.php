<?php

namespace Teddy\GameModule\Presenters;

class LogoutPresenter extends BasePresenter
{

	public function actionDefault()
	{
		$this->getUser()->logout();
		$this->flashMessage('You have been signed out.');
		$this->redirect(':Index:Homepage:default');
	}

}
