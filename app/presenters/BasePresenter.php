<?php

namespace App\Presenters;

use Nette,
	App\Model;


abstract class BasePresenter extends Nette\Application\UI\Presenter
{

    /** @var \App\Model\Bans @inject */
    public $bans;

    protected function startup()
    {
        parent::startup();
        $ban = $this->bans->hasTotalBan($_SERVER['REMOTE_ADDR']);
        if($ban) {
            $this->error('Your IP is banned until ' . $ban->getUntil()->format('j.m.Y H:i:s') . ': ' . $ban->getReason(), 403);
        }
    }
}
