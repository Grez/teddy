<?php

namespace Teddy\GameModule\Presenters;

use Teddy\Entities\Map\Map;
use Teddy\GameModule\Components\IMapControlFactory;



class MainPresenter extends BasePresenter
{

	/**
	 * @var IMapControlFactory
	 * @inject
	 */
	public $mapControlFactory;



	protected function  createComponentMap()
	{
		$map = $this->em->find(Map::class, 41);
		return $this->mapControlFactory->create($map);
	}

}
