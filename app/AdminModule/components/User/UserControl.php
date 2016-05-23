<?php

namespace Teddy\AdminModule\Components;

use Game\Entities\Logs\UserLog;
use Game\Entities\User\User;
use Nette\Utils\ArrayHash;
use Nette\Utils\Strings;
use Teddy;
use Teddy\Entities\Logs\UserLogs;
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
	 * @var array
	 */
	public $onDaemon = [];

	/**
	 * @var EntityManager
	 */
	protected $em;

	/**
	 * @var \Game\Entities\User\User
	 */
	protected $editedUser;

	/**
	 * @var Teddy\Security\UserContext
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

	/**
	 * @var UserLogs
	 */
	private $logs;



	public function __construct($salt, User $editedUser, EntityManager $em, Users $users, Teddy\Security\UserContext $userContext, ImageService $imageService, UserLogs $logs)
	{
		parent::__construct();
		$this->salt = $salt;
		$this->em = $em;
		$this->editedUser = $editedUser;
		$this->userContext = $userContext;
		$this->users = $users;
		$this->imageService = $imageService;
		$this->logs = $logs;
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
		$nick = $this->editedUser->getNick();
		$this->users->markDeleted($this->editedUser);
		$this->logs->logAdminAction($this->userContext->getEntity(), UserLog::ADMIN_DELETE_USER, $nick);
		$this->onUserDeleted($this, $this->editedUser);
	}



	public function handleReactivate()
	{
		$this->users->reactivate($this->editedUser);
		$this->logs->logAdminAction($this->userContext->getEntity(), UserLog::ADMIN_REACTIVATE_USER, $this->editedUser->getNick());
		$this->onUserReactivated($this, $this->editedUser);
	}



	/**
	 * Logs in as user, redirects to :Game:Homepage
	 */
	public function handleLoginAsUser()
	{
		$this->userContext->passwordLessLogin($this->editedUser);
		$this->logs->logAdminAction($this->userContext->getEntity(), UserLog::ADMIN_DAEMON, $this->editedUser->getNick());
		$this->onDaemon($this, $this->editedUser);
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



	/**
	 * Changes user info
	 *
	 * @param Form $form
	 * @param ArrayHash $values
	 */
	public function editUserFormSuccess(Form $form, ArrayHash $values)
	{
		$personal = $values->user->personal;
		if (isset($personal->deleteAvatar) && $personal->deleteAvatar)  {
			$this->editedUser->deleteAvatar($this->imageService);
		}

		$oldNick = $this->editedUser->getNick();
		$this->users->update($this->editedUser, $values);
		$this->logs->logAdminAction($this->userContext->getEntity(), UserLog::ADMIN_EDIT_USER, $this->editedUser->getNick());
		if ($oldNick !== $values->nick) {
			$this->logs->logAdminAction($this->userContext->getEntity(), UserLog::ADMIN_CHANGE_USER_NICK, [$oldNick, $values->nick]);
		}
		$this->onUserEdited($this, $this->editedUser);
	}



	/**
	 * @return Form
	 */
	protected function createComponentPasswordChangeForm()
	{
		$form = new Form();
		$form->addPassword('password_new', 'New password')
			->setRequired();
		$form->addSubmit('send', 'Submit');
		$form->onSuccess[] = $this->changePasswordFormSuccess;
		return $form->setBootstrapRenderer();
	}



	/**
	 * Changes User password and logs it
	 *
	 * @param Form $form
	 * @param ArrayHash $values
	 */
	public function changePasswordFormSuccess(Form $form, ArrayHash $values)
	{
		$this->users->changePassword($this->editedUser, $values->password_new);
		$this->logs->logAdminAction($this->userContext->getEntity(), UserLog::ADMIN_CHANGE_PASSWORD, $this->editedUser->getNick());
		$this->onUserPasswordChange($this, $this->editedUser);
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
