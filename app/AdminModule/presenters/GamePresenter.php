<?php

namespace Teddy\AdminModule\Presenters;

use Teddy\AdminModule\Components\IStatsControlFactory;



class GamePresenter extends \Game\AdminModule\Presenters\BasePresenter
{
	/**
	 * @var IStatsControlFactory
	 * @inject
	 */
	public $statsControlFactory;


	protected function createComponentStats()
	{
		return $this->statsControlFactory->create();
	}

}
