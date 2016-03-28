<?php

namespace Teddy\GameModule\Presenters;

use Teddy\Entities\Map\Map;
use Teddy\Entities\Map\Position;
use Teddy\GameModule\Components\IMapControlFactory;



class MainPresenter extends BasePresenter
{

	/**
	 * @var IMapControlFactory
	 * @inject
	 */
	public $mapControlFactory;



	protected function createComponentMap()
	{
		$map = $this->em->find(Map::class, 56);
		$startPosition = $this->em->find(Position::class, '56;-37;-34');
		return $this->mapControlFactory->create($map, $startPosition);
	}

}
