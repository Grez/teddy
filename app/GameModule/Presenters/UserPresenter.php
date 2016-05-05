<?php

namespace Teddy\GameModule\Presenters;

use Nette\Http\FileUpload;
use Nette\Utils\ArrayHash;
use Teddy\Forms\Form;
use Teddy\Services\ImageService;



class UserPresenter extends \Game\GameModule\Presenters\BasePresenter
{

	/**
	 * @var ImageService
	 * @inject
	 */
	public $imageService;



	public function renderDefault()
	{
		$query = (new \Teddy\Entities\User\UserListQuery());
		$result = $this->users->fetch($query);
		$result->applyPaginator($this['visualPaginator']->getPaginator(), 20);
		$this->template->players = $result;
	}



	/**
	 * @param string $id (Player's nick)
	 */
	public function renderDetail($id = NULL)
	{
		if ($id) {
			$player = $this->users->getByNick($id);
			if ($player == NULL) {
				$this->flashMessage('This user doesn\'t exist', 'error');
				$this->redirect('default');
			}
			$this->template->player = $player;
		}
		$this->template->imageService = $this->imageService;
	}



	/**
	 * @return Form
	 */
	protected function createComponentUpdateUserForm()
	{
		$form = new Form();
		$form['user'] = new \Teddy\Forms\User\UserContainer();
		$form['user']['personal'] = new \Teddy\Forms\User\PersonalContainer();
		if (!$this->user->hasAvatar()) {
			unset($form['user']['personal']['deleteAvatar']);
		}

		$form->addSubmit('send', 'Submit');
		$form->onSuccess[] = $this->updateUserFormSuccess;
		$form->bindEntity($this->user);
		return $form;
	}



	/**
	 * @param Form $form
	 * @param ArrayHash $values
	 */
	public function updateUserFormSuccess(Form $form, ArrayHash $values)
	{
		$values->avatar = $this->processAvatar($form, $values);
		$this->users->update($this->user, $values);
		$this->successFlashMessage('Your info has been updated.');
		$this->redirect('this');
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
			$this->user->deleteAvatar($this->imageService);
		}
		unset($personal->deleteAvatar);

		/** @var FileUpload $avatar */
		$avatar = $personal->avatar;
		if ($avatar->isOk()) {
			$this->user->deleteAvatar($this->imageService);

			$filename = $avatar->getSanitizedName();
			$path = $this->imageService->getAvatarPath() . '/' . $filename;
			$avatar->move($path);
			return $filename;
		}

		return $this->user->avatar;
	}



	/**
	 * @return Form
	 */
	protected function createComponentChangePasswordForm()
	{
		$form = new Form();
		$form->addPassword('password', 'Current password')
			->addRule([$this->users, 'validatePassword'], 'You\'ve entered wrong password.', $this->user->getId())
			->setRequired();
		$form->addPassword('password_new', 'New password')
			->setRequired();
		$form->addPassword('password_again', 'Password again')
			->setRequired()
			->addRule(Form::EQUAL, 'Passwords do not match', $form['password_new']);
		$form->onSuccess[] = $this->changePasswordSuccess;
		$form->addSubmit('send', 'Submit');
		return $form;
	}



	/**
	 * @param Form $form
	 * @param ArrayHash $values
	 */
	public function changePasswordSuccess(Form $form, ArrayHash $values)
	{
		$this->users->changePassword($this->user, $values->password_new);
		$this->flashMessage('Your password has been changed');
		$this->redirect('this');
	}



	/**
	 * @return Form
	 */
	protected function createComponentChangeEmailForm()
	{
		$form = new Form();
		$form->addPassword('password', 'Current password')
			->addRule([$this->users, 'validatePassword'], 'You\'ve entered wrong password.', $this->user->getId())
			->setRequired();
		$form->addText('email', 'New e-mail')
			->addRule(Form::EMAIL, 'Please enter valid e-mail.')
			->setRequired();
		$form->onSuccess[] = $this->changeEmailSuccess;
		$form->addSubmit('send', 'Submit');
		return $form;
	}



	/**
	 * @param Form $form
	 * @param ArrayHash $values
	 */
	public function changeEmailSuccess(Form $form, ArrayHash $values)
	{
		$this->user->setEmail($values->email);
		$this->users->save($this->user);
		$this->flashMessage('Your e-mail has been changed');
		$this->redirect('this');
	}

}
