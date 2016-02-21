<?php

namespace Teddy\AdminModule\Presenters;

use Nette\Utils\ArrayHash;
use Teddy\Forms\Form;
use Teddy\User\UserDoesNotExistException;

/**
 * @TODO: user info, daemon, ban?, referrals
 */
class UsersPresenter extends BasePresenter
{

	/**
	 * @param string $user
	 */
	public function renderUser($user)
	{
		$user = $this->users->getByNick($user);
		if (!$user) {
			$this->warningFlashMessage('This user doesn\'t exist');
			$this->redirect('default');
		}

		$this->template->editedUser = $user;
	}


	/**
	 * Logs out user (destroys all his session)
	 *
	 * @param int $userId
	 */
	public function handleLogoutUser($userId)
	{
		$this->redirect('this');
	}



	/**
	 * Logs in as user, redirects to :Game:Homepage
	 *
	 * @param int $userId
	 */
	public function handleLoginAsUser($userId)
	{
		try {
			$this->getUser()->passwordLessLogin();
		} catch (UserDoesNotExistException $e) {
			$this->warningFlashMessage('This user doesn\'t exist');
			$this->redirect('this');
		}

		$this->redirect(':Game:Homepage:');
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
			$this->redirect('this', ['userId' => $user->getId()]);
		};
		return $form->setBootstrapRenderer();
	}

}
