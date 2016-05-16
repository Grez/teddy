<?php

namespace Teddy\GameModule\Presenters;

use Teddy\IndexModule\Components\ChangeEmailControl;
use Teddy\IndexModule\Components\ChangePasswordControl;
use Teddy\IndexModule\Components\IChangeEmailControlFactory;
use Teddy\IndexModule\Components\IChangePasswordControlFactory;
use Teddy\IndexModule\Components\IUserInfoControlFactory;
use Teddy\IndexModule\Components\UserInfoControl;



class UserPresenter extends \Game\GameModule\Presenters\BasePresenter
{

	/**
	 * @var IChangePasswordControlFactory
	 * @inject
	 */
	public $changePasswordFactory;

	/**
	 * @var IChangeEmailControlFactory
	 * @inject
	 */
	public $changeEmailFactory;

	/**
	 * @var IUserInfoControlFactory
	 * @inject
	 */
	public $userInfoFactory;



	public function renderDefault()
	{
		$query = (new \Teddy\Entities\User\UserListQuery());
		$result = $this->users->fetch($query);
		$result->applyPaginator($this['visualPaginator']->getPaginator(), 20);
		$this->template->players = $result;
	}



	/**
	 * @param string $id (Player's nick)
	 */
	public function renderDetail($id = NULL)
	{
		if ($id) {
			$player = $this->users->getByNick($id);
			if ($player == NULL) {
				$this->flashMessage('This user doesn\'t exist', 'error');
				$this->redirect('default');
			}
			$this->template->player = $player;
		}
	}



	/**
	 * @return UserInfoControl
	 */
	protected function createComponentUserInfo()
	{
		$control = $this->userInfoFactory->create();
		$control->onSuccess[] = function (UserInfoControl $self) {
			$this->successFlashMessage('Your info has been changed');
			$this->refreshPage('this', $this->snippetsToRefresh);
		};
		return $control;
	}



	/**
	 * @return ChangePasswordControl
	 */
	protected function createComponentChangePassword()
	{
		$control = $this->changePasswordFactory->create();
		$control->onSuccess[] = function (ChangePasswordControl $self) {
			$this->successFlashMessage('Your password has been changed');
			$this->refreshPage('this', $this->snippetsToRefresh);
		};
		return $control;
	}



	/**
	 * @return ChangeEmailControl
	 */
	protected function createComponentChangeEmail()
	{
		$control = $this->changeEmailFactory->create();
		$control->onSuccess[] = function (ChangeEmailControl $self) {
			$this->successFlashMessage('Your e-mail has been changed');
			$this->refreshPage('this', $this->snippetsToRefresh);
		};
		return $control;
	}

}
