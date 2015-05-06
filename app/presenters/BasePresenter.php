<?php

namespace Teddy\Presenters;

use Nette;
use Teddy\Model;

abstract class BasePresenter extends Nette\Application\UI\Presenter
{

    /** @var \Kdyby\Doctrine\EntityManager @inject */
    public $em;

    /** @var \Teddy\Model\Bans @inject */
    public $bans;

    /** @var \Teddy\Model\Users @inject */
    public $users;

    /** @var \Teddy\FrontEndCompiler\Loader @inject */
    public $frontEndCompiler;


    protected function beforeRender()
    {
        parent::beforeRender();
        $this->frontEndCompiler->publicizeDirs();
        $this->template->header = array('css' => '', 'js' => '');
        $this->template->header['css'] = $this->frontEndCompiler->getCss();
        $this->template->header['js'] = $this->frontEndCompiler->getJs();
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
