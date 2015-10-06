<?php

namespace Teddy\IndexModule\Presenters;

use Teddy\Forms\Form;



class ForgottenPasswordPresenter extends BasePresenter
{

	/** @var \Teddy\Entities\User\User */
	protected $user;



	/**
	 * @param int $token
	 */
	public function actionSetNew($token)
	{
		$this->user = $this->users->getByToken($token);
		if (!$this->user) {
			$this->flashMessage('This token isn\'t valid or has already expired. Please ask for another e-mail.', 'error');
			$this->redirect('default');
		}
	}



	/**
	 * @return Form
	 */
	public function createComponentGenerateTokenForm()
	{
		$form = new Form();
		$form->addText('nick', 'Nick')
			->setRequired();
		$form->addSubmit('submit', 'Submit');
		$form->onSuccess[] = $this->generateTokenFormSuccess;
		return $form;
	}



	/**
	 * @param Form $form
	 * @param \Nette\Utils\ArrayHash $values
	 */
	public function generateTokenFormSuccess(Form $form, $values)
	{
		$user = $this->users->getByNick($values->nick);
		if (!$user) {
			$this->flashMessage('This user doesn\'t exist.', 'error');
			$this->redirect('this');
		}

		$token = $user->generateToken();
		$this->em->flush();
		$this->flashMessage('Your token was sent to ' . $user->getEmail(TRUE) . ' (' . $token . ')'); // here come dragons... I mean, e-mail!
		$this->redirect('this');
	}



	/**
	 * @return Form
	 */
	public function createComponentSetNewForm()
	{
		$form = new Form();
		$form->addPassword('password', 'New password')
			->setRequired();
		$form->addPassword('password_again', 'Password again')
			->setRequired()
			->addRule(Form::EQUAL, 'Passwords must be equal', $form['password']);
		$form->addSubmit('submit', 'Submit');
		$form->onSuccess[] = $this->setNewFormSuccess;
		return $form;
	}



	public function setNewFormSuccess(Form $form, $values)
	{
		$password = \Nette\Security\Passwords::hash($values->password);
		$this->user->setPassword($password);
		$this->user->invalidateToken();
		$this->em->flush();
		$this->flashMessage('Your password has been changed. You may login now.');
		$this->redirect('Homepage:default');
	}
}
