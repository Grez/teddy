<?php

namespace Teddy\IndexModule\Presenters;

use Nette\Utils\ArrayHash;
use Teddy\Entities\Bans\Ban;
use Teddy\Forms\Form;



class HomepagePresenter extends \Game\IndexModule\Presenters\BasePresenter
{

	/**
	 * @return Form
	 */
	public function createComponentRegistrationForm()
	{
		$form = new Form();
		$ban = $this->bans->hasRegistrationBan($_SERVER['REMOTE_ADDR']);
		if ($ban) {
			$form->addError('Your IP is banned until ' . $ban->getEndsAt()->format('j.m.Y H:i:s') . ': ' . $ban->getReason());
		} else {
			$form->addText('nick', 'Nick')
				->addRule([$this->users, 'validateNick'], 'This username is already taken.')
				->setRequired();
			$form->addText('email', 'E-mail')
				->addRule(Form::EMAIL, 'Please enter valid e-mail.')
				->setRequired();
			$form->addPassword('password', 'Password')
				->setRequired();
			$form->addPassword('password_again', 'Password again')
				->setRequired()
				->addRule(Form::EQUAL, 'Passwords must be equal', $form['password']);
			$form->addSubmit('submit', 'Submit');
		}

		$form->onSuccess[] = $this->registrationFormSuccess;
		return $form;
	}



	/**
	 * @param Form $form
	 * @param ArrayHash $values
	 */
	public function registrationFormSuccess(Form $form, $values)
	{
		$this->users->register($values);
		$this->successFlashMessage('Your registration was successful');
		$this->redirect('this');
	}



	/**
	 * @return Form
	 */
	public function createComponentLoginForm()
	{
		$form = new Form();
		$ban = $this->bans->hasGameBan($_SERVER['REMOTE_ADDR']);

		if ($ban) {
			$form->addError('Your IP is banned until ' . $ban->getEndsAt()->format('j.m.Y H:i:s') . ': ' . $ban->getReason());
		} else {
			$form->addText('nick', 'Nick')
				->setRequired();
			$form->addPassword('password', 'Password')
				->setRequired();
			$form->addSubmit('submit', 'Submit');
			$form->onSuccess[] = $this->loginFormSuccess;
		}
		return $form;
	}



	/**
	 * @param Form $form
	 * @param ArrayHash $values
	 */
	public function loginFormSuccess(Form $form, $values)
	{
		try {
			$this->getUser()->login($values->nick, $values->password);
			$this->successFlashMessage('You were successfully logged in.');
			$this->redirect(':Game:Main:');

		} catch (\Nette\Security\AuthenticationException $e) {
			$this->warningFlashMessage($e->getMessage());
			$this->redirect('this');
		}
	}

}
