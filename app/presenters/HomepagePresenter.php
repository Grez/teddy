<?php

namespace App\Presenters;

use App\Model\Users;
use App\Model\User;
use Nette\Application\UI\Form;


class HomepagePresenter extends BasePresenter
{

    /** @var \Kdyby\Doctrine\EntityManager @inject */
    public $em;

    /** @var Users @inject */
    public $users;


    public function renderDefault()
    {
        $user = $this->users->getByNick('grez');
    }

    /**
     * @return Form
     */
    public function createComponentRegistrationForm()
    {
        $form = new Form();
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
        $form->addText('login', 'Nick or email')
            ->setRequired();
        $form->addPassword('password', 'Password')
            ->setRequired();
        $form->addSubmit('submit', 'Submit');
        $form->onSuccess[] = $this->loginFormSuccess;
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
