<?php

use Teddy\Entities\Bans\Position;

require __DIR__ . '/../app/entities/Map/Position.php';
require __DIR__ . '/../vendor/autoload.php';


abstract class Map
{

	/**
	 * @var int
	 */
	private $size = 0;

	/**
	 * @var Position[]
	 */
	private $positions = [];



	/**
	 * Makes map bigger by $embiggenBy
	 * http://cs.urbandictionary.com/define.php?term=Embiggen
	 *
	 * @param int $embiggenBy
	 * @return $this
	 */
	public function embiggenBy($embiggenBy)
	{
		for ($i = 0; $i < $embiggenBy; $i++) {
			$this->embiggen();
		}
		return $this;
	}



	public function embiggen()
	{
		for ($x = $this->size * -1; $x <= $this->size + 1; $x++) {
			$this->createPosition($x, $this->size + 1);
		}

		for ($y = $this->size; $y >= $this->size * -1 - 1; $y--) {
			$this->createPosition($this->size + 1, $y);
		}

		for ($x = $this->size; $x >= $this->size * -1 - 1; $x--) {
			$this->createPosition($x, $this->size * -1 - 1);
		}

		for ($y = $this->size * -1; $y <= $this->size + 1; $y++) {
			$this->createPosition($this->size * -1 - 1, $y);
		}

		$this->size++;
		return $this;
	}



	/**
	 * @param Position $position
	 * @return $this
	 */
	public function addPosition(Position $position)
	{
		$this->positions[$position->getId()] = $position;
		return $this;
	}



	/**
	 * @param int $x
	 * @param int $y
	 * @return Position|NULL
	 */
	public function getPosition($x, $y)
	{
		return isset($this->positions[$x . ';' . $y]) ? $this->positions[$x . ';' . $y] : NULL;
	}



	public function getNeighbours($x, $y)
	{
		$neighbours = [];
		$coords = [
			[$x, $y+1],
			[$x+1, $y+1],
			[$x-1, $y+1],
			[$x, $y-1],
			[$x+1, $y-1],
			[$x-1, $y-1],
			[$x+1, $y],
			[$x-1, $y],
		];

		foreach ($coords as $coord) {
			$neighbour = $this->getPosition($coord[0], $coord[1]);
			if ($neighbour) {
				$neighbours[] = $neighbour;
			}
		}

		return $neighbours;
	}



	public function getFarNeighbours($x, $y)
	{
		$neighbours = [];
		$coords = [
			[$x, $y+2],
			[$x+2, $y+2],
			[$x-2, $y+2],
			[$x, $y-2],
			[$x+2, $y-2],
			[$x-2, $y-2],
			[$x+2, $y],
			[$x-2, $y],
		];

		foreach ($coords as $coord) {
			$neighbour = $this->getPosition($coord[0], $coord[1]);
			if ($neighbour) {
				$neighbours[] = $neighbour;
			}
		}

		return $neighbours;
	}



	public function createPosition($x, $y)
	{

	}



	public function debug()
	{
		echo count($this->positions) . '<br><br>';
		foreach ($this->positions as $position) {
			echo $position->getId() . ': ' . $position->getHeight() . '<br>';
		}
	}


	public function render()
	{
		$html = '<table style="width: ' . $this->size * 20 . 'px;">';
		for ($x = $this->size * -1; $x < $this->size; $x++) {
			$html .= '<tr>';
			for ($y = $this->size * -1; $y < $this->size; $y++) {
				$position = $this->getPosition($x, $y);
				if (!$position) {
					continue;
				}
				$html .= '<td class="type' . $position->getHeight() . '" title="' . $position->getId() . '"></td>';
			}
			$html .= '</tr>';
		}
		return $html;
	}


}



class Earth extends Map
{

	/**
	 * @var array
	 */
	private $params;



	public function __construct($size)
	{
//		$this->params = $params;
		$this->createPosition(0, 0);
		$this->embiggenBy($size);
	}



	public function createPosition($x, $y)
	{
		$perlin = new \NoiseGenerator\PerlinNoise(3000);
		$octaves = array(32, 16, 4, 2);
		$num = $perlin->noise($x, $y, 0, $octaves);
		$height = ($num / 2) + .5;

		$position = new Position($x, $y, $height);
		$this->addPosition($position);
	}

}

echo '<pre>';
$earth = new Earth(25);
//print_r($earth->debug());

echo '<style>
* {box-sizing: border-box;}
td {width: 10px; height: 10px; border: 1px solid #333; padding: 0;}
.typeB {background: #000;}
.typeG {background: #666;}
.typeD {background: #66a;}
.typeS {background: #fa0;}
.typeW {background: #aa2;}
.type0 {background: #333;}
.type1 {background: #00f;}
.type2 {background: #ff9;}
.type3 {background: #5c5;}
.type4 {background: #4b4;}
.type5 {background: #3a3;}
.type6 {background: #292;}
.type7 {background: #181;}
.type8 {background: #b93;}
.type9 {background: #a82;}
.type10 {background: #ccc;}
.type11 {background: #fff;}
table {border-collapse: collapse;}
</style>';

//echo $earth->render();

$earth->embiggenBy(25);
echo $earth->render();
