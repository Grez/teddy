<?php

namespace Teddy\AdminModule\Presenters;

use Nette;
use Teddy;
use Teddy\Forms\Form;
use Teddy\Model\AdminPermission;


class AdminsPresenter extends BasePresenter
{

    /** @var array */
    protected $admins = array();


    public function startup()
    {
        parent::startup();
        $this->admins = $this->users->getAdmins();
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
     * @param \Nette\Utils\ArrayHash $values
     */
    public function createAdminFormSuccess(Form $form, $values)
    {
        $user = $this->users->getByNick($values['user']);
        if (!$user) {
            $this->flashMessage('This user doesn\'t exist', 'danger');
            return '';
        }

        if ($user->isAdmin()) {
            $this->flashMessage('This user is already an admin', 'danger');
            return '';
        }

        $user->setAdmin(true);
        $this->users->save($user);
        $this->flashMessage('Admin created');
        $this->redirect('this');
    }

    /**
     * @return Nette\Application\UI\Multiplier
     */
    protected function createComponentAdminForm()
    {
        return new Nette\Application\UI\Multiplier(function($id) {
            $admin = $this->users->find($id);
            $form = new Form();
            $form->addHidden('id', $admin->getId());
            $form->addText('adminDescription', 'Description')
                ->setDefaultValue($admin->getAdminDescription());
            $form->addText('lastLogin', 'Last login')
                ->setDisabled()
                ->setDefaultValue($admin->getLastLogin()->format('Y-m-d H:i:s'));
            $form->addText('lastActivity', 'Last activity')
                ->setDisabled()
                ->setDefaultValue($admin->getLastActivity()->format('Y-m-d H:i:s'));
            $form->addCheckboxList('adminPermissions', 'Permissions', $this->sections)
                ->setDefaultValue($admin->getAdminPermissions(true))
                ->getSeparatorPrototype()->setName('inline');
            $form->addSubmit('send', 'Edit');
            $form->addSubmit('delete', 'Delete')
                ->onClick[] = array($this, 'adminFormDelete');
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
            $this->flashMessage('This user isn\'t admin', 'danger');
            $this->redirect('this');
        }

        $this->users->deleteAdmin($admin);
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
        $this->flashMessage('Admin edited', 'success');
        $this->redirect('this');
    }

}