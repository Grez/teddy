<?php

namespace Teddy\Presenters;

use Nette;
use Teddy\Entities;
use IPub\VisualPaginator\Components as VisualPaginator;



/**
 * @method \Teddy\Security\User getUser()
 * @property-read \Teddy\Security\User $user
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{

	/** @var \Kdyby\Doctrine\EntityManager @inject */
	public $em;

	/** @var \Teddy\Entities\Bans\Bans @inject */
	public $bans;

	/** @var \Teddy\Entities\User\Users @inject */
	public $users;

	/** @var \WebLoader\Nette\LoaderFactory @inject */
	public $webLoader;



	/**
	 * @return \WebLoader\Nette\CssLoader
	 */
	protected function createComponentCss()
	{
		return $this->webLoader->createCssLoader('default');
	}



	/**
	 * @return \WebLoader\Nette\JavaScriptLoader
	 */
	protected function createComponentJs()
	{
		return $this->webLoader->createJavaScriptLoader('default');
	}



	protected function startup()
	{
		parent::startup();
		$ban = $this->bans->hasTotalBan($_SERVER['REMOTE_ADDR']);
		if ($ban) {
			$this->error('Your IP is banned until ' . $ban->getEndsAt()->format('j.m.Y H:i:s') . ': ' . $ban->getReason(), 403);
		}
	}



	/**
	 * @return VisualPaginator\Control
	 */
	protected function createComponentVisualPaginator()
	{
		$control = new VisualPaginator\Control;
		$control->setTemplateFile('bootstrap.latte');
		$control->getPaginator()->setItemsPerPage(20);
		$control->disableAjax();
		return $control;
	}



	/**
	 * @param string $message
	 */
	protected function infoFlashMessage($message)
	{
		$this->flashMessage($message, 'info');
	}



	/**
	 * @param string $message
	 * @deprecated use $this->warningFlashMessage instead
	 */
	protected function errorFlashMessage($message)
	{
		$this->warningFlashMessage($message);
	}



	/**
	 * @param string $message
	 */
	protected function warningFlashMessage($message)
	{
		$this->flashMessage($message, 'warning');
	}



	/**
	 * @param string $message
	 */
	protected function successFlashMessage($message)
	{
		$this->flashMessage($message, 'success');
	}



	/**
	 * Invalidates snippets on ajax / redirects to itself on normal request
	 * @param string $redirect
	 * @param string[] $snippets
	 */
	protected function refreshPage($redirect = 'this', array $snippets = NULL)
	{
		if ($this->isAjax()) {
			if ($snippets) {
				foreach ($snippets as $snippet) {
					$this->redrawControl($snippet);
				}

			} else {
				$this->redrawControl();
			}

		} else {
			$this->redirect($redirect);
		}
	}

}
