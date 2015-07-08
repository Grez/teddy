<?php

namespace Teddy\Presenters;

use Nette;
use Teddy\Model;
use IPub\VisualPaginator\Components as VisualPaginator;

abstract class BasePresenter extends Nette\Application\UI\Presenter
{

    /** @var \Kdyby\Doctrine\EntityManager @inject */
    public $em;

    /** @var \Teddy\Model\Bans @inject */
    public $bans;

    /** @var \Teddy\Model\Users @inject */
    public $users;

    /** @var \WebLoader\Nette\LoaderFactory @inject */
    public $webLoader;


    /** @return CssLoader */
    protected function createComponentCss()
    {
        return $this->webLoader->createCssLoader('default');
    }

    /** @return JavaScriptLoader */
    protected function createComponentJs()
    {
        return $this->webLoader->createJavaScriptLoader('default');
    }

    protected function startup()
    {
        parent::startup();
        $ban = $this->bans->hasTotalBan($_SERVER['REMOTE_ADDR']);
        if($ban) {
            $this->error('Your IP is banned until ' . $ban->getUntil()->format('j.m.Y H:i:s') . ': ' . $ban->getReason(), 403);
        }
    }

    /**
     * @return VisualPaginator\Control
     */
    protected function createComponentVisualPaginator()
    {
        $control = new VisualPaginator\Control;
        $control->setTemplateFile('bootstrap.latte');
        return $control;
    }

}

