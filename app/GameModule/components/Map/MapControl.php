<?php

namespace Teddy\GameModule\Components;

use Teddy;
use Teddy\Entities\Map\Map;
use Nette\Application\UI\Control;
use Teddy\Entities\Map\Position;
use Teddy\Security\User;


class MapControl extends Control
{

	/**
	 * @var User
	 */
	protected $user;

	/**
	 * @var Map
	 */
	protected $map;

	/**
	 * @var Position
	 */
	protected $startPosition;



	public function __construct(Map $map, Teddy\Security\User $user, Position $startPosition)
	{
		parent::__construct();
		$this->map = $map;
		$this->user = $user;
		$this->startPosition = $startPosition;
	}



	public function render()
	{
		$template = parent::createTemplate();
		$template->map = $this->map;
		$template->startPosition = $this->startPosition;
		$template->setFile(__DIR__ . '/map.latte');
		$template->render();
	}

}



interface IMapControlFactory
{

	/**
	 * @param Map $map
	 * @param Position $startPosition
	 * @return MapControl
	 */
	public function create(Map $map, Position $startPosition);
}
