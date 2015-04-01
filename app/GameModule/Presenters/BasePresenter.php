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

        $user = $this->getUser();
        if(!$user->isLoggedIn()) {
            $this->flashMessage(_('You are not logged in'), 'error');
            $this->redirect(':Homepage:default');
        }

        $this->user = $this->users->find($user->id);
        $this->template->user = $this->user;
    }
}