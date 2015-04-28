<?php

namespace Teddy\AdminModule\Presenters;

use Nette;
use Teddy;
use Teddy\Forms\Form;
use Teddy\Model\UserLog;


class MainPresenter extends BasePresenter
{

    /** @var Persistent */
    public $page;

    /** @var \Teddy\Model\UserLogs @inject */
    public $userLogs;


    public function renderDefault()
    {
        $this->template->logs = $this->userLogs->getLogs(UserLog::ADMIN);
    }

    protected function createComponentAdminLogsFilterForm()
    {
        $form = new Form();
        $form->addText('user', 'User');
        $form->addSelect('action', 'Action', AdminLog::getActions());
        $form->onSuccess[] = $this->adminLogsFilterFormSuccess;
        return $form;
    }

    public function adminLogsFilterFormSuccess(Form $form, $values)
    {

    }

}
