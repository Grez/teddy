<?php

namespace Teddy\AdminModule\Presenters;

use Nette\Utils\ArrayHash;
use Teddy\AdminModule\Components\IUserControlFactory;
use Teddy\AdminModule\Components\IUserControlFactory2;
use Teddy\AdminModule\Components\UserControl;
use Teddy\Entities\User\Player;
use Teddy\Forms\Form;
use Teddy\User\UserDoesNotExistException;

/**
 * @TODO: user info, referrals
 * @TODO: logout user
 */
class UsersPresenter extends \Game\AdminModule\Presenters\BasePresenter
{

	/**
	 * @var \Game\Entities\User\Player|NULL
	 */
	protected $editedUser;

	/**
	 * @var IUserControlFactory
	 * @inject
	 */
	public $userControlFactory;


	/**
	 * @param int (act. string) $userId
	 */
	public function actionUser($userId)
	{
		$this->editedUser = $this->users->find($userId);
		if (!$this->editedUser) {
			$this->warningFlashMessage('This user doesn\'t exist');
			$this->redirect('default');
		}
	}


	/**
	 * @param int (act. string) $userId
	 */
	public function renderUser($userId)
	{
		$this->template->editedUser = $this->editedUser;
	}



	/**
	 * @return Form
	 */
	protected function createComponentSearchUserForm()
	{
		$form = new Form();
		$form->addText('nick', 'Nick')
			->setRequired();
		$form->addSubmit('send', 'Find');
		$form->onValidate[] = function (Form $form, ArrayHash $values) {
			$user = $this->users->getByNick($values->nick);
			if (!$user) {
				$form->addError('This user doesn\'t exist');
			}
		};
		$form->onSuccess[] = function (Form $form, ArrayHash $values) {
			$user = $this->users->getByNick($values->nick);
			$this->redirect('user', ['userId' => $user->getId()]);
		};
		return $form->setBootstrapRenderer();
	}



	/**
	 * @return Form
	 */
	protected function createComponentUser()
	{
		$control = $this->userControlFactory->create($this->editedUser);
		$control->onUserEdited = function (UserControl $userControl, Player $user) {
			$this->successFlashMessage('User edited');
			$this->redirect('this');
		};
		$control->onUserDeleted = function (UserControl $userControl, Player $user) {
			$this->successFlashMessage('User deleted');
			$this->redirect('this');
		};
		$control->onUserReactivated = function (UserControl $userControl, Player $user) {
			$this->successFlashMessage('User reactivated');
			$this->redirect('this');
		};
		$control->onUserPasswordChange = function (UserControl $userControl, Player $user) {
			$this->successFlashMessage('User\'s password changed');
			$this->redirect('this');
		};
		$control->onDaemon = function (UserControl $userControl, Player $user) {
			$this->successFlashMessage('You were logged in as user');
			$this->redirect(':Game:Main:');
		};
		return $control;
	}

}
