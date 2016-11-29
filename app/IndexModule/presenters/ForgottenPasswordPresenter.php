<?php

namespace Teddy\IndexModule\Presenters;

use Nette\Mail\Message;
use Teddy\Forms\Form;



class ForgottenPasswordPresenter extends \Game\IndexModule\Presenters\BasePresenter
{

	/**
	 * @var \Teddy\Entities\User\User|NULL
	 */
	protected $player;



	/**
	 * @param int $token
	 */
	public function actionSetNew($token)
	{
		$this->player = $this->users->getByToken($token);
		if (!$this->player) {
			$this->warningFlashMessage('This token isn\'t valid or has already expired. Please ask for another e-mail.');
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
			$this->warningFlashMessage('This user doesn\'t exist.');
			return;
		}


		$token = $user->generateToken();
		$this->em->flush();

		$mail = new Message();
		$mail->addTo($user->getEmail());
		$mail->setFrom('no-reply@teddy.cz', 'No-reply');
		$mail->setSubject('Changing password');
		$link = $_SERVER['HTTP_ORIGIN'] . $this->link('setNew', ['token' => $token]);
		$mail->setHtmlBody(sprintf('You may change your password on this <a href="%s">link</a>', $link));
		$this->mailer->send($mail);

		$this->successFlashMessage('Your token was sent to ' . $user->getAnonymizedEmail()); // here come dragons... I mean, e-mail!
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
		$this->player->setPassword($password);
		$this->player->invalidateToken();
		$this->em->flush();
		$this->successFlashMessage('Your password has been changed. You may login now.');
		$this->redirect('Homepage:default');
	}
}
