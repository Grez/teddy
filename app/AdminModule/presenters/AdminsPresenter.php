<?php

namespace Teddy\AdminModule\Presenters;

use Nette;
use Nette\Utils\ArrayHash;
use Teddy\Entities\User\UserListQuery;
use Teddy\Forms\Form;
use Teddy\Entities\Logs\UserLog;



class AdminsPresenter extends BasePresenter
{

	/** @var array */
	protected $admins = [];



	public function startup()
	{
		parent::startup();
		$query = (new UserListQuery())->onlyAdmins();
		$this->admins = $this->users->fetch($query);
		$this->template->admins = $this->admins;
	}



	/**
	 * @return Form
	 */
	protected function createComponentCreateAdminForm()
	{
		$form = new Form();
		$form->addText('user', 'User')
			->setRequired();
		$form->addSubmit('send', 'Add');
		$form->onSuccess[] = $this->createAdminFormSuccess;
		return $form;
	}



	/**
	 * @param Form $form
	 * @param ArrayHash $values
	 * @return void
	 * @throws Nette\Application\AbortException
	 */
	public function createAdminFormSuccess(Form $form, ArrayHash $values)
	{
		$user = $this->users->getByNick($values['user']);
		if (!$user) {
			$this->warningFlashMessage('This user doesn\'t exist');
			return;
		}

		if ($user->isAdmin()) {
			$this->warningFlashMessage('This user is already an admin');
			return;
		}

		$user->setAdmin(TRUE);
		$this->users->save($user);
		$this->userLogs->log($this->user, UserLog::ADMIN, UserLog::ADMIN_CREATE_ADMIN, $user->getNick());
		$this->flashMessage('Admin created');
		$this->redirect('this');
	}



	/**
	 * @return Nette\Application\UI\Multiplier
	 */
	protected function createComponentAdminForm()
	{
		return new Nette\Application\UI\Multiplier(function ($id) {
			$admin = $this->users->find($id);
			$form = new Form();
			$form->addHidden('id', $admin->getId());
			$form->addText('adminDescription', 'Description')
				->setDefaultValue($admin->getAdminDescription());
			$form->addText('lastLogin', 'Last login')
				->setDisabled()
				->setDefaultValue($admin->getLastLoginAt()->format('Y-m-d H:i:s'));
			$form->addText('lastActivity', 'Last activity')
				->setDisabled()
				->setDefaultValue($admin->getLastActivityAt()->format('Y-m-d H:i:s'));
			$form->addCheckboxList('adminPermissions', 'Permissions', $this->getPresenters())
				->setDefaultValue($admin->getAdminPermissions(TRUE))
				->getSeparatorPrototype()->setName('inline');
			$form->addSubmit('send', 'Edit');
			$form->addSubmit('delete', 'Delete')
				->onClick[] = [$this, 'adminFormDelete'];
			$form->onSuccess[] = $this->adminFormSuccess;
			return $form;
		});
	}



	/**
	 * @param Nette\Forms\Controls\SubmitButton $button
	 */
	public function adminFormDelete(Nette\Forms\Controls\SubmitButton $button)
	{
		$id = $button->getForm()->getValues()->id;
		$admin = $this->users->find($id);

		if (!$admin->isAdmin()) {
			$this->warningFlashMessage('This user isn\'t admin');
			$this->redirect('this');
		}

		$this->users->deleteAdmin($admin);
		$this->userLogs->log($this->user, UserLog::ADMIN, UserLog::ADMIN_DELETE_ADMIN, $admin->getNick());
		$this->flashMessage('Admin deleted', 'success');
		$this->redirect('this');
	}



	/**
	 * @param Form $form
	 * @param $values
	 */
	public function adminFormSuccess(Form $form, $values)
	{
		$admin = $this->users->find($values->id);
		$this->users->setAdminPermissions($admin, $values['adminPermissions']);
		$admin->setAdminDescription($values['adminDescription']);
		$this->users->save($admin);
		$this->userLogs->log($this->user, UserLog::ADMIN, UserLog::ADMIN_EDIT_ADMIN, $admin->getNick());
		$this->successFlashMessage('Admin edited');
		$this->redirect('this');
	}

}
