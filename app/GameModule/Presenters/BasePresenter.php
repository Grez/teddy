<?php

namespace Teddy\GameModule\Presenters;

use Teddy\Model\Users;
use Teddy\Model\User;
use Teddy;
use Nette;

abstract class BasePresenter extends Teddy\Presenters\BasePresenter
{

    /** @var User */
    protected $user;


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