<?php

namespace Teddy\IndexModule\Components;

use Nette\Utils\ArrayHash;
use Teddy;
use Teddy\Entities\User\Users;
use Teddy\Forms\Form;
use Kdyby\Doctrine\EntityManager;
use Game\Components\Control;
use Teddy\Security\User;
use Teddy\TemplateHelpers;



/**
 * @method onSuccess(ChangePasswordControl $this)
 */
class ChangePasswordControl extends Control
{

	/**
	 * @var array
	 */
	public $onSuccess = [];

	/**
	 * @var array
	 */
	public $onError = [];

	/**
	 * @var EntityManager
	 */
	protected $em;

	/**
	 * @var Users
	 */
	protected $users;

	/**
	 * @var User
	 */
	protected $user;

	/**
	 * @var string
	 */
	protected $template = 'changePassword';



	public function __construct(TemplateHelpers $templateHelpers, EntityManager $em, Users $users, User $user)
	{
		parent::__construct($templateHelpers);
		$this->em = $em;
		$this->users = $users;
		$this->user = $user;
	}



	/**
	 * @return Form
	 */
	protected function createComponentForm()
	{
		$form = new Form();
		$form->setAjax(TRUE);
		$form->addPassword('password', 'Current password')
			->addRule([$this->users, 'validatePassword'], 'You\'ve entered wrong password.', $this->user->getId())
			->setRequired();
		$form->addPassword('password_new', 'New password')
			->setRequired();
		$form->addPassword('password_again', 'Password again')
			->setRequired()
			->addRule(Form::EQUAL, 'Passwords do not match', $form['password_new']);
		$form->onSuccess[] = $this->formSuccess;
		$form->onError[] = $this->formError;
		$form->addSubmit('send', 'Submit');
		return $form->setBootstrapRenderer();
	}



	/**
	 * @param Form $form
	 * @param ArrayHash $values
	 */
	public function formSuccess(Form $form, ArrayHash $values)
	{
		$this->users->changePassword($this->user->getEntity(), $values->password_new);
		$this['form']['password']->setValue(NULL);
		$this['form']['password_new']->setValue(NULL);
		$this['form']['password_again']->setValue(NULL);
		$this->redrawControl();
		$this->onSuccess($this);
	}



	/**
	 * @param Form $form
	 */
	public function formError(Form $form)
	{
		$this->redrawControl();
	}



	public function render()
	{
		$template = parent::createTemplate();
		$template->setFile(__DIR__ . '/' . $this->template . '.latte');
		$template->render();
	}

}



interface IChangePasswordControlFactory
{

	/** @return ChangePasswordControl */
	function create();
}
