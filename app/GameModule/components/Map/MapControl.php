<?php

namespace Teddy\GameModule\Components;

use Teddy;
use Teddy\Entities\Map\Map;
use Teddy\Entities\Map\Position;
use Nette\Application\UI\Control;
use Teddy\Security\User;


class MapControl extends Control
{

	/**
	 * @var User
	 */
	private $user;

	/**
	 * @var Map
	 */
	private $map;



	public function __construct(Map $map)
	{
		$this->map = $map;
	}



	public function render()
	{
		$template = parent::createTemplate();
		$template->map = $this->map;
		$template->setFile(__DIR__ . '/map.latte');
		$template->render();
	}

}



interface IMapControlFactory
{

	/** @return MapControl */
	function create(Map $map);
}
