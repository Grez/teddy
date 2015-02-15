<?php

namespace App\GameModule\Presenters;

use App\Model\User;
use App;
use Nette;

abstract class BasePresenter extends Nette\Application\UI\Presenter
{
    /** @var User */
    protected $user;

    /** @var \Kdyby\Doctrine\EntityManager @inject */
    public $em;


    protected function startup()
    {
        parent::startup();
        $uid = 1;
        $this->user = $this->em->find('User', $uid);
        if(!$this->user) {
            $this->flashMessage(_('You are not logged in'), 'error');
            $this->redirect('/');
        }
    }
}