<?php

namespace App\Presenters;

use Nette;
use App\Model;

abstract class BasePresenter extends Nette\Application\UI\Presenter
{

    /** @var \Kdyby\Doctrine\EntityManager @inject */
    public $em;

    /** @var \App\Model\Bans @inject */
    public $bans;

    /** @var \App\Model\Users @inject */
    public $users;

    /** @var \App\Security\User */
    private $user;


    public function injectUser(\App\Security\User $user)
    {
        $this->user = $user;
    }

    protected function startup()
    {
        parent::startup();
        $ban = $this->bans->hasTotalBan($_SERVER['REMOTE_ADDR']);
        if($ban) {
            $this->error('Your IP is banned until ' . $ban->getUntil()->format('j.m.Y H:i:s') . ': ' . $ban->getReason(), 403);
        }
    }
}
