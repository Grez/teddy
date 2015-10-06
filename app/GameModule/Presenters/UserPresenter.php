<?php

namespace Teddy\GameModule\Presenters;

use Teddy\Forms\Form;



class UserPresenter extends BasePresenter
{

	public function renderDefault()
	{
		$query = (new \Teddy\Entities\User\UserListQuery())->onlyActivated();
		$result = $this->users->fetch($query);
		$result->applyPaginator($this['visualPaginator']->getPaginator(), 20);
		$this->template->players = $result;
	}



	/**
	 * @param string $id (Player's nick)
	 */
	public function renderDetail($id = '')
	{
		if ($id != '') {
			$player = $this->users->getByNick($id);
			if ($player == NULL) {
				$this->flashMessage('This user doesn\'t exist', 'error');
				$this->redirect('default');
			}
			$this->template->player = $player;
		}
	}



	/**
	 * @return Form
	 */
	protected function createComponentUpdateUserForm()
	{
		$form = new Form();
		$form['user'] = new \Teddy\Forms\User\UserContainer();
		$form['user']['personal'] = new \Teddy\Forms\User\PersonalContainer();
		$form->addSubmit('send', 'Submit');
		$form->onSuccess[] = $this->updateUserFormSuccess;
		$form->bindEntity($this->user);
		return $form;
	}



	/**
	 * @TODO: img upload
	 * @param Form $form
	 * @param \Nette\Utils\ArrayHash $values
	 */
	public function updateUserFormSuccess(Form $form, $values)
	{
		$user = $this->users->find($values['user']['id']);
		if (!$user->canEdit($this->user)) {
			$this->flashMessage('You can\'t edit this user', 'error');
			$this->redirect('this');
		}

		$this->users->update($user, $values);
		$this->flashMessage('Your info has been updated.');
		$this->redirect('this');
	}



	/**
	 * @return Form
	 */
	protected function createComponentChangePasswordForm()
	{
		$form = new Form();
		$form->addPassword('password', 'Current password')
			->addRule(array($this->users, 'validatePassword'), 'You\'ve entered wrong password.', $this->user->getId())
			->setRequired();
		$form->addPassword('password_new', 'New password')
			->setRequired();
		$form->addPassword('password_again', 'Password again')
			->setRequired()
			->addRule(Form::EQUAL, 'Passwords do not match', $form['password_new']);
		$form->onSuccess[] = $this->changePasswordSuccess;
		$form->addSubmit('send', 'Submit');
		return $form;
	}



	/**
	 * @param Form $form
	 * @param \Nette\Utils\ArrayHash $values
	 */
	public function changePasswordSuccess(Form $form, $values)
	{
		$this->users->changePassword($this->user, $values['password_new']);
		$this->flashMessage('Your password has been changed');
		$this->redirect('this');
	}



	/**
	 * @return Form
	 */
	protected function createComponentChangeEmailForm()
	{
		$form = new Form();
		$form->addPassword('password', 'Current password')
			->addRule(array($this->users, 'validatePassword'), 'You\'ve entered wrong password.', $this->user->getId())
			->setRequired();
		$form->addText('email', 'New e-mail')
			->addRule(Form::EMAIL, 'Please enter valid e-mail.')
			->setRequired();
		$form->onSuccess[] = $this->changeEmailSuccess;
		$form->addSubmit('send', 'Submit');
		return $form;
	}



	/**
	 * @param Form $form
	 * @param \Nette\Utils\ArrayHash $values
	 */
	public function changeEmailSuccess(Form $form, $values)
	{
		$this->user->setEmail($values['email']);
		$this->users->save($this->user);
		$this->flashMessage('Your e-mail has been changed');
		$this->redirect('this');
	}

}
