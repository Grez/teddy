<?php

namespace Teddy\IndexModule\Components;

use Game\Components\Control;
use Nette\Http\FileUpload;
use Nette\Utils\ArrayHash;
use Teddy;
use Teddy\Entities\User\Users;
use Teddy\Forms\Form;
use Kdyby\Doctrine\EntityManager;
use Teddy\Security\User;
use Teddy\Images\ImageService;
use Teddy\TemplateHelpers;



/**
 * @method onSuccess(UserInfoControl $this)
 */
class UserInfoControl extends Control
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
	 * @var ImageService
	 */
	protected $imageService;

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
	protected $template = 'userInfo';



	public function __construct(TemplateHelpers $templateHelpers, EntityManager $em, Users $users, User $user, ImageService $imageService)
	{
		parent::__construct($templateHelpers);
		$this->em = $em;
		$this->users = $users;
		$this->user = $user;
		$this->imageService = $imageService;
	}



	/**
	 * @return Form
	 */
	protected function createComponentForm()
	{
		$form = new Form();
		$form->setAjax(TRUE);
		$form['user'] = new \Teddy\Forms\User\UserContainer();
		$form['user']['personal'] = new \Teddy\Forms\User\PersonalContainer();
		$form['user']['personal']['avatar']->setOption('avatar', $this->user->getEntity()->getAvatar());

		$form->addSubmit('send', 'Submit');
		$form->onSuccess[] = $this->formSuccess;
		$form->onError[] = $this->onError;
		$form->bindEntity($this->user->getEntity());
		return $form->setBootstrapRenderer();
	}



	/**
	 * @param Form $form
	 * @param ArrayHash $values
	 */
	public function formSuccess(Form $form, ArrayHash $values)
	{
		$values->avatar = $this->processAvatar($form, $values);
		$this->users->update($this->user->getEntity(), $values);

		$form['user']['personal']['avatar']->setOption('avatar', $this->user->getEntity()->getAvatar());
		$form->bindEntity($this->user->getEntity());

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



	/**
	 * @param Form $form
	 * @param ArrayHash $values
	 * @return string|NULL filename
	 */
	protected function processAvatar(Form $form, ArrayHash $values)
	{
		$personal = $values->user->personal;
		if (isset($personal->deleteAvatar) && $personal->deleteAvatar)  {
			$this->user->getEntity()->deleteAvatar($this->imageService);
		}
		unset($personal->deleteAvatar);

		/** @var FileUpload $avatar */
		$avatar = $personal->avatar;
		if ($avatar->isOk()) {
			$this->user->getEntity()->deleteAvatar($this->imageService);

			$filename = $avatar->getSanitizedName();
			$path = $this->imageService->getAvatarPath() . '/' . $filename;
			$avatar->move($path);
			return $filename;
		}

		return $this->user->getEntity()->avatar;
	}



	public function render()
	{
		$template = parent::createTemplate();
		$template->setFile(__DIR__ . '/' . $this->template . '.latte');
		$template->render();
	}

}



interface IUserInfoControlFactory
{

	/** @return UserInfoControl */
	function create();
}
