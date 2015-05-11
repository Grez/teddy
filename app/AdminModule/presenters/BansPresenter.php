<?php

namespace Teddy\AdminModule\Presenters;

use Teddy\Forms\Form;
use Teddy\Model\Ban;
use Teddy\Model\Bans;
use Teddy\Model\UserLog;

class BansPresenter extends BasePresenter
{

    /** @var Bans @inject */
    public $bans;


    public function startup()
    {
        parent::startup();
        $this->template->bans = $this->bans->getBans();
    }

    public function actionDelete($id)
    {
        $ban = $this->bans->find($id);
        $this->bans->delete($ban);
        $this->userLogs->log($this->user, UserLog::ADMIN, UserLog::ADMIN_UNBAN_IP, array($ban->getIp(), $ban->getReason()));
        $this->flashMessage('Ban has been deleted', 'success');
        $this->redirect('default');
    }

    protected function createComponentIpBanForm()
    {
        $form = new Form();
        $form->addText('reason', 'Reason')
            ->setRequired();
        $form->addSelect('type', 'Type', array(
            Ban::REGISTRATION => 'Registration (user may play from this IP but can\'t register new profiles)',
            Ban::GAME => 'Game (default)',
            Ban::TOTAL => 'Total (DoS attacks etc., return 403 error for request)',
        ))->setDefaultValue(Ban::GAME);
        $form->addText('days', 'Days')
            ->addCondition(Form::NUMERIC);
        $form->addText('ip', 'IP')
            ->setRequired()
            ->setAttribute('placeholder', '143.12.123.123, or 143.12.123.*');
        $form->addSubmit('send', 'Ban');
        $form->onSuccess[] = $this->ipBanFormSuccess;
        return $form;
    }

    public function ipBanFormSuccess(Form $form, $values)
    {
        $days = (($values['days'] > 0) ? $values['days'] : '∞');
        $this->bans->ban($values['ip'], $values['reason'], $values['days'], $values['type']);
        $this->userLogs->log($this->user, UserLog::ADMIN, UserLog::ADMIN_BAN_IP, array($values['ip'], $days, $values['reason']));
        $this->flashMessage('Ban has been created', 'success');
        $this->redirect('this');
    }

}