<?php

namespace App\Presenters;


use Nette\Application\UI\Form;


class HomepagePresenter extends BasePresenter
{

    /**
     * @return Form
     */
    public function createComponentRegistrationForm()
    {
        $form = new Form();
        $ban = $this->bans->hasRegistrationBan($_SERVER['REMOTE_ADDR']);
        if ($ban) {
            $form->addError('Your IP is banned until ' . $ban->getUntil()->format('j.m.Y H:i:s') . ': ' . $ban->getReason());
        } else {
            $form->addText('nick', 'Nick')
                ->addRule(array($this->users, 'validateNick'), 'This username is already taken.')
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
     * @param \Nette\Utils\ArrayHash $values
     */
    public function registrationFormSuccess(Form $form, $values)
    {
        $this->users->register($values);
        $this->flashMessage('Your registrion was successful');
        $this->redirect('this');
    }

    /**
     * @return Form
     */
    public function createComponentLoginForm()
    {
        $form = new Form();
        $ban = $this->bans->hasLoginBan($_SERVER['REMOTE_ADDR']);
        if ($ban) {
            $form->addError('Your IP is banned until ' . $ban->getUntil()->format('j.m.Y H:i:s') . ': ' . $ban->getReason(), 'Error');
        } else {
            $form->addText('login', 'Nick or email')
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
     * @param \Nette\Utils\ArrayHash $values
     */
    public function loginFormSuccess(Form $form, $values)
    {
        try {
            $this->getUser()->login($values->login, $values->password);

            $this->flashMessage('You were successfully logged in.');
            $this->redirect('Game:Main:');
        } catch (\Nette\Security\AuthenticationException $e) {
            $this->flashMessage($e->getMessage(), 'error');
            $this->redirect('this');
        }
    }

}
