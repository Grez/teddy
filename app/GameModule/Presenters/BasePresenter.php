<?php

namespace App\GameModule\Presenters;

use App\Model\Users;
use App\Model\User;
use App;
use Nette;

abstract class BasePresenter extends Nette\Application\UI\Presenter
{
    /** @var User */
    protected $user;

    /** @var \Kdyby\Doctrine\EntityManager @inject */
    public $em;

    /** @var Users @inject */
    public $users;


    protected function startup()
    {
        parent::startup();
        $uid = 1;
        $this->user = $this->users->find($uid);
        if(!$this->user) {
            $this->flashMessage(_('You are not logged in'), 'error');
            $this->redirect(':Homepage:default');
        }

        $this->template->user = $this->user;
    }

    public function actionLogout()
    {
        $this->getUser()->logout();
        $this->flashMessage('You have been signed out.');
        $this->redirect(':Homepage:default');
    }
}