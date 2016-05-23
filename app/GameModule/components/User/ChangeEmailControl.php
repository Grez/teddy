<?php

namespace Teddy\IndexModule\Components;

use Nette\Utils\ArrayHash;
use Teddy;
use Teddy\Entities\User\Users;
use Teddy\Forms\Form;
use Kdyby\Doctrine\EntityManager;
use Game\Components\Control;
use Teddy\Security\UserContext;
use Teddy\TemplateHelpers;



/**
 * @method onSuccess(ChangeEmailControl $this)
 */
class ChangeEmailControl extends Control
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
	 * @var UserContext
	 */
	protected $user;

	/**
	 * @var string
	 */
	protected $template = 'changeEmail';



	public function __construct(TemplateHelpers $templateHelpers, EntityManager $em, Users $users, UserContext $user)
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
		$form->addText('email', 'New e-mail')
			->addRule(Form::EMAIL, 'Please enter valid e-mail.')
			->setRequired();
		$form->onSuccess[] = $this->formSuccess;
		$form->onError[] = $this->formError;
		$form->addSubmit('send', 'Submit');
		return $form;
	}



	/**
	 * @param Form $form
	 * @param ArrayHash $values
	 */
	public function formSuccess(Form $form, ArrayHash $values)
	{
		$this->user->getEntity()->setEmail($values->email);
		$this->users->save($this->user->getEntity());
		$this['form']['password']->setValue(NULL);
		$this['form']['email']->setValue(NULL);
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



interface IChangeEmailControlFactory
{

	/** @return ChangeEmailControl */
	function create();
}
