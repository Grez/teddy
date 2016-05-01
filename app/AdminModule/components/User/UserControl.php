<?php

namespace Teddy\AdminModule\Components;

use Game\Entities\User\User;
use Nette\Utils\ArrayHash;
use Teddy;
use Teddy\Entities\User\Users;
use Teddy\Forms\Form;
use Kdyby\Doctrine\EntityManager;
use Nette\Application\UI\Control;



/**
 * @method void onUserEdited(UserControl $this, User $user)
 */
class UserControl extends Control
{

	/**
	 * @var array
	 */
	public $onUserEdited;

	/**
	 * @var EntityManager
	 */
	protected $em;

	/**
	 * @var \Game\Entities\User\User
	 */
	protected $editedUser;

	/**
	 * @var Teddy\Security\User
	 */
	protected $userContext;

	/**
	 * @var
	 */
	protected $salt;

	/**
	 * @var Users
	 */
	protected $users;



	public function __construct($salt, User $editedUser, EntityManager $em, Users $users, Teddy\Security\User $userContext)
	{
		parent::__construct();
		$this->salt = $salt;
		$this->em = $em;
		$this->editedUser = $editedUser;
		$this->userContext = $userContext;
		$this->users = $users;
	}



	public function render()
	{
		$template = parent::createTemplate();
		$template->editedUser = $this->editedUser;
		$template->showUsersWithSamePassword = (bool) $this->salt;
		$template->usersWithSamePassword = $this->getUsersWithSamePassword();
		$template->setFile(__DIR__ . '/user.latte');
		$template->render();
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
			->setDefaultValue($this->editedUser->getRegisteredAt()->format('Y-m-d H:i:s'));
		$form->addText('lastActivity', 'Last activity')
			->setDisabled()
			->setDefaultValue($this->editedUser->getLastActivityAt()->format('Y-m-d H:i:s'));
		$form->addSubmit('send', 'Save');
		$form->onSuccess[] = $this->editUserFormSuccess;
		return $form->setBootstrapRenderer();
	}



	public function editUserFormSuccess(Form $form, ArrayHash $values)
	{
		$this->users->update($this->editedUser, $values);
		$this->onUserEdited($this, $this->editedUser);
		$this->redirect('this');
	}



	/**
	 * Returns other Users with same password
	 *
	 * @return User[]
	 */
	protected function getUsersWithSamePassword()
	{
		$users = $this->users->getByPassword($this->editedUser->getPassword());
		return array_filter($users, function (User $user) {
			return $user !== $this->editedUser;
		});
	}
}



interface IUserControlFactory
{

	/**
	 * @param User $editedUser
	 * @return UserControl
	 */
	public function create(User $editedUser);
}
