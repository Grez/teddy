<?php

namespace Teddy\Entities\Map;



use Kdyby\Doctrine\EntityManager;
use NoiseGenerator\PerlinNoise;



/**
 * @method void onEmbiggen(MapService $mapService, Map $map)
 */
class MapService extends \Nette\Object
{

	/**
	 * @var EntityManager
	 */
	private $em;

	/**
	 * @var array
	 */
	public $onEmbiggen = [];



	public function __construct(EntityManager $em)
	{
		$this->em = $em;
	}



	/**
	 * @param int $radius
	 * @return Map
	 */
	public function createMap($radius)
	{
		$map = new Map();
		$this->em->persist($map);
		$this->em->flush($map);
		$this->embiggenMapBy($map, $radius);
		return $map;
	}



	/**
	 * Makes map bigger by $embiggenBy
	 * http://cs.urbandictionary.com/define.php?term=Embiggen
	 *
	 * @param Map $map
	 * @param int $embiggenBy
	 * @return Map
	 */
	public function embiggenMapBy(Map $map, $embiggenBy)
	{
		for ($i = 0; $i < $embiggenBy; $i++) {
			$positions = $this->addBorderToMap($map);
			foreach ($positions as $position) {
				$this->em->persist($position);
			}

			// We want to do this in single trasactions
			$this->em->flush(array_merge($map, $positions));
			$this->em->clear(Position::class);
			$this->onEmbiggen($this, $map);
		}

		return $map;
	}



	/**
	 * We add border
	 * Generated in this order ("=" are starting positions, number represents step)
	 *
	 * 1112
	 * 4==2
	 * 4==2
	 * 4333
	 *
	 * @param Map $map
	 * @return Position[]
	 */
	protected function addBorderToMap(Map $map)
	{
		// There is nothing in map
		if ($map->getRadius() === 0) {
			$map->increaseMaxDistance();
			return [$this->createPosition($map, 0, 0)];
		}

		$newPositions = [];
		for ($x = $map->getRadius() * -1; $x <= $map->getRadius() - 1; $x++) {
			$newPositions[] = $this->createPosition($map, $x, $map->getRadius());
		}

		for ($y = $map->getRadius(); $y >= $map->getRadius() * -1 + 1; $y--) {
			$newPositions[] = $this->createPosition($map, $map->getRadius(), $y);
		}

		for ($x = $map->getRadius(); $x >= $map->getRadius() * -1 + 1; $x--) {
			$newPositions[] = $this->createPosition($map, $x, $map->getRadius() * -1);
		}

		for ($y = $map->getRadius() * -1; $y <= $map->getRadius() - 1; $y++) {
			$newPositions[] = $this->createPosition($map, $map->getRadius() * -1, $y);
		}

		$map->increaseMaxDistance();
		return $newPositions;
	}



	/**
	 * @param int
	 * @return Map
	 */
	public function getMap($id)
	{
		return $this->em->find(Map::class, $id);
	}



	/**
	 * @param Map $map
	 * @param int $x
	 * @param int $y
	 * @param bool $addToMap - when the position isn't added to map we don't have to fetch already generated positions
	 * @return Position
	 */
	protected function createPosition(Map $map, $x, $y, $addToMap = FALSE)
	{
		$perlin = new PerlinNoise($map->getSeed());
		$num = $perlin->noise($x, $y, 0, $map->getOctaves());
		$height = ($num / 2) + 0.5;

		$position = new Position($map, $x, $y, $height);
		if ($addToMap) {
			$map->addPosition($position);
		}
		return $position;
	}

}
