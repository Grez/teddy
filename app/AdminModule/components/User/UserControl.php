<?php

namespace Teddy\AdminModule\Components;

use Game\Entities\User\User;
use Nette\Utils\ArrayHash;
use Nette\Utils\Strings;
use Teddy;
use Teddy\Entities\User\Users;
use Teddy\Forms\Form;
use Kdyby\Doctrine\EntityManager;
use Nette\Application\UI\Control;
use Teddy\Images\ImageService;



/**
 * @method void onUserEdited(UserControl $this, User $user)
 * @method void onUserDeleted(UserControl $this, User $user)
 * @method void onUserReactivated(UserControl $this, User $user)
 * @method void onUserPasswordChange(UserControl $this, User $user)
 */
class UserControl extends Control
{

	/**
	 * @var array
	 */
	public $onUserEdited = [];

	/**
	 * @var array
	 */
	public $onUserDeleted = [];

	/**
	 * @var array
	 */
	public $onUserReactivated = [];

	/**
	 * @var array
	 */
	public $onUserPasswordChange = [];

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
	 * From config.local.neon
	 * If isset all hashed password use this salt
	 *
	 * @var string
	 */
	protected $salt;

	/**
	 * @var Users
	 */
	protected $users;

	/**
	 * @var ImageService
	 */
	protected $imageService;



	public function __construct($salt, User $editedUser, EntityManager $em, Users $users, Teddy\Security\User $userContext, ImageService $imageService)
	{
		parent::__construct();
		$this->salt = $salt;
		$this->em = $em;
		$this->editedUser = $editedUser;
		$this->userContext = $userContext;
		$this->users = $users;
		$this->imageService = $imageService;
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



	public function handleDelete()
	{
		$this->users->markDeleted($this->editedUser);
		$this->onUserDeleted($this, $this->editedUser);
	}



	public function handleReactivate()
	{
		$this->users->reactivate($this->editedUser);
		$this->onUserReactivated($this, $this->editedUser);
	}



	/**
	 * @return Form
	 */
	protected function createComponentEditUserForm()
	{
		$form = new Form();
		$form->addText('nick', 'Nick')
			->setRequired();
		$form->addText('registered', 'Registered')
			->setDisabled()
			->setDefaultValue($this->editedUser->getRegisteredAt()->format('Y-m-d H:i:s'));
		$form->addText('lastActivity', 'Last activity')
			->setDisabled()
			->setDefaultValue($this->editedUser->getLastActivityAt()->format('Y-m-d H:i:s'));

		$form['user'] = new \Teddy\Forms\User\UserContainer();
		$form['user']['personal'] = new \Teddy\Forms\User\PersonalContainer();
		unset($form['user']['personal']['avatar']);
		if (!$this->editedUser->hasAvatar()) {
			unset($form['user']['personal']['deleteAvatar']);
		}
		$form->bindEntity($this->editedUser);

		$form->addSubmit('send', 'Save');
		$form->onSuccess[] = $this->editUserFormSuccess;
		return $form->setBootstrapRenderer();
	}



	public function editUserFormSuccess(Form $form, ArrayHash $values)
	{
		$personal = $values->user->personal;
		if (isset($personal->deleteAvatar) && $personal->deleteAvatar)  {
			$this->editedUser->deleteAvatar($this->imageService);
		}
		$this->users->update($this->editedUser, $values);

		$this->onUserEdited($this, $this->editedUser);
		$this->redirect('this');
	}



	/**
	 * @return Form
	 */
	protected function createComponentPasswordChangeForm()
	{
		$form = new Form();
		$form->addPassword('password_new', 'New password')
			->setRequired();
		$form->onSuccess[] = function (Form $form, ArrayHash $values) {
			$this->users->changePassword($this->editedUser, $values->password_new);
			$this->onUserPasswordChange($this, $this->editedUser);
			$this->redirect('this');
		};
		$form->addSubmit('send', 'Submit');
		return $form->setBootstrapRenderer();
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
