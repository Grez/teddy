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
            $this->flashMessage('This user doesn\'t exist', 'error');
            return '';
        }

        if ($user->isAdmin()) {
            $this->flashMessage('This user is already an admin', 'error');
            return '';
        }

        $user->setAdmin(true);
        $this->users->save($user);
        $this->flashMessage('Admin created');
        $this->redirect('this');
    }

    /**
     * @return Form
     */
    protected function createComponentAdminPermissionsForm()
    {
        $form = new Form();
        foreach ($this->admins as $admin) {
            $form->addGroup($admin->getNick());
            $container = $form->addContainer($admin->getId());
            $container->addText('adminDescription', 'Description')
                ->setDefaultValue($admin->getAdminDescription());
            $container->addText('lastLogin', 'Last login')
                ->setDisabled()
                ->setDefaultValue($admin->getLastLogin()->format('Y-m-d H:i:s'));
            $container->addText('lastActivity', 'Last activity')
                ->setDisabled()
                ->setDefaultValue($admin->getLastActivity()->format('Y-m-d H:i:s'));
            $container->addCheckboxList('adminPermissions', 'Permissions', $this->sections)
                ->setDefaultValue($admin->getAdminPermissions(true))
                ->getSeparatorPrototype()->setName('inline');
        }
        $form->setCurrentGroup();
        $form->addSubmit('send', 'Edit');
        $form->onSuccess[] = $this->adminPermissionsFormSuccess;
        return $form;
    }

    /**
     * @TODO: how to do this better?
     * @param Form $form
     * @param $values
     */
    public function adminPermissionsFormSuccess(Form $form, $values)
    {
        foreach ($values as $id => $data) {
            $admin = $this->users->find($id);
            $this->users->setAdminPermissions($admin, $data['adminPermissions']);
            $admin->setAdminDescription($data['adminDescription']);
            $this->users->save($admin);
        }
        $this->flashMessage('Permissions updated');
        $this->redirect('this');
    }

}