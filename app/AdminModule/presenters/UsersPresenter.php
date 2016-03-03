<?php

namespace Teddy\AdminModule\Presenters;

use Nette\Utils\ArrayHash;
use Teddy\Entities\User\User;
use Teddy\Forms\Form;
use Teddy\User\UserDoesNotExistException;

/**
 * @TODO: user info, referrals
 * @TODO: logout user
 */
class UsersPresenter extends BasePresenter
{

	/**
	 * @var User|NULL
	 */
	protected $editedUser;



	/**
	 * @param string $user
	 */
	public function renderUser($userId)
	{
		$this->editedUser = $this->users->find($userId);
		if (!$this->editedUser) {
			$this->warningFlashMessage('This user doesn\'t exist');
			$this->redirect('default');
		}

		$this->template->editedUser = $this->editedUser;
	}



	/**
	 * Logs in as user, redirects to :Game:Homepage
	 *
	 * @param int $userId
	 */
	public function handleLoginAsUser($userId)
	{
		/** @var \Teddy\Entities\User\User $user */
		$user = $this->users->find($userId);
		if (!$user) {
			$this->warningFlashMessage('This user doesn\'t exist');
			$this->redirect('this');
		}

		$this->getUser()->passwordLessLogin($user->getId());
		$this->redirect(':Game:Main:');
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
	protected function createComponentEditUserForm()
	{
		$form = new Form();
		$form->addText('nick', 'Nick')
			->setRequired()
			->setDefaultValue($this->editedUser->getNick());
		$form->addText('registered', 'Registered')
			->setDisabled()
			->setDefaultValue($this->editedUser->getRegistered()->format('Y-m-d H:i:s'));
		$form->addText('lastActivity', 'Last activity')
			->setDisabled()
			->setDefaultValue($this->editedUser->getLastActivity()->format('Y-m-d H:i:s'));
		$form->addSubmit('send', 'Save');
		return $form->setBootstrapRenderer();
	}

}
