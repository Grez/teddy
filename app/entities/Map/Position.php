<?php

namespace Teddy\Entities\Map;

use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Nette;
use Teddy\Entities;
use Doctrine\ORM\Mapping as ORM;



/**
 * @ORM\Entity(readOnly=TRUE)
 */
class Position
{

	/**
	 * @ORM\Id()
	 * @ORM\Column(type="string")
	 * @var string
	 */
	protected $id;

	/**
	 * @ORM\ManyToOne(targetEntity="Map", inversedBy="positions")
	 * @var Map
	 */
	protected $map;

	/**
	 * @ORM\Column(type="integer")
	 * @var int
	 */
	protected $x;

	/**
	 * @ORM\Column(type="integer")
	 * @var int
	 */
	protected $y;

	/**
	 * @ORM\Column(type="float")
	 * @var float
	 */
	protected $height;



	/**
	 * @parma Map $map
	 * @param int $x
	 * @param int $y
	 * @param float $height
	 */
	public function __construct(Map $map, $x, $y, $height)
	{
		$this->map = $map;
		$this->x = $x;
		$this->y = $y;
		$this->height = $height;
		$this->id = $map->getId() . ';' . $this->x . ';' . $this->y;
	}



	/**
	 * @return string
	 */
	public function getId()
	{
		return $this->id;
	}



	/**
	 * @return float
	 */
	public function getHeight()
	{
		return round($this->height * 10);
	}



	/**
	 * @return float
	 */
	public function getWeight()
	{
		return $this->height < 0.2 ? 0 : $this->height + 1;
	}



	/**
	 * @return Map
	 */
	public function getMap()
	{
		return $this->map;
	}



	/**
	 * X position, same as in db; top left position is "-radius + 1;-radius + 1"
	 *
	 * @return int
	 */
	public function getRealX()
	{
		return $this->x;
	}



	/**
	 * Y position, same as in db; top left position is "-radius + 1;-radius + 1"
	 *
	 * @return int
	 */
	public function getRealY()
	{
		return $this->y;
	}



	/**
	 * Returns X as if map started from 0;0 instead of "-radius + 1;-radius + 1"
	 *
	 * @return int
	 */
	public function getVirtualX()
	{
		return $this->x + $this->map->getRadius() - 1;
	}



	/**
	 * Returns Y as if map started from 0;0 instead of "-radius + 1;-radius + 1"
	 *
	 * @return int
	 */
	public function getVirtualY()
	{
		return $this->y + $this->map->getRadius() - 1;
	}

}
