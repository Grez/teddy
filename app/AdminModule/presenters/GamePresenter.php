<?php

namespace Teddy\AdminModule\Presenters;

use Teddy\AdminModule\Components\IStatsControlFactory;



/**
 * @TODO: maintenance, restart?
 */
class GamePresenter extends BasePresenter
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
