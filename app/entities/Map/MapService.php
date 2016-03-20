<?php

namespace Teddy\Entities\Map;



use Kdyby\Doctrine\EntityManager;



class MapService
{

	/**
	 * @var EntityManager
	 */
	private $em;



	public function __construct(EntityManager $em)
	{
		$this->em = $em;
	}



	/**
	 * @param int $radius
	 * @return Map
	 */
	public function createMap($radius = 25)
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
		$positions = [];
		for ($i = 0; $i < $embiggenBy; $i++) {
			$positions = array_merge($this->embiggenMap($map), $positions);
		}

		foreach ($positions as $position) {
			$this->em->persist($position);
		}

		$this->em->flush();
		return $map;
	}



	/**
	 * Enlarges map by two
	 *
	 * @param Map $map
	 * @return Position[]
	 */
	protected function embiggenMap(Map $map)
	{
		if ($map->getRadius() < 1 && count($map->getPositions()) === 0) {
			return [$this->createPosition($map, 0, 0)];
		}

		$newPositions = [];
		for ($x = $map->getRadius() * -1; $x <= $map->getRadius() + 1; $x++) {
			$newPositions[] = $this->createPosition($map, $x, $map->getRadius() + 1);
		}

		for ($y = $map->getRadius(); $y >= $map->getRadius() * -1 - 1; $y--) {
			$newPositions[] = $this->createPosition($map, $map->getRadius() + 1, $y);
		}

		for ($x = $map->getRadius(); $x >= $map->getRadius() * -1 - 1; $x--) {
			$newPositions[] = $this->createPosition($map, $x, $map->getRadius() * -1 - 1);
		}

		for ($y = $map->getRadius() * -1; $y <= $map->getRadius() + 1; $y++) {
			$newPositions[] = $this->createPosition($map, $map->getRadius() * -1 - 1, $y);
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
	 * @param int $x
	 * @param int $y
	 * @param bool $addToMap - when the position isn't added to map we don't have to fetch already generated positions
	 * @return Position
	 */
	public function createPosition(Map $map, $x, $y, $addToMap = FALSE)
	{
		$perlin = new \NoiseGenerator\PerlinNoise($map->getSeed());
		$num = $perlin->noise($x, $y, 0, $map->getOctaves());
		$height = ($num / 2) + .5;

		$position = new Position($map, $x, $y, $height);
		if ($addToMap) {
			$map->addPosition($position);
		}
		return $position;
	}

}
