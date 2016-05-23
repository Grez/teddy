<?php

namespace Teddy\Entities\Stats;

use Doctrine\ORM\Mapping as ORM;
use Nette;



trait StatsTrait
{

	/**
	 * @ORM\Column(type="integer")
	 * @var int
	 */
	protected $playersTotal = 0;

	/**
	 * @ORM\Column(type="integer")
	 * @var int
	 */
	protected $playersActive = 0;

	/**
	 * @ORM\Column(type="integer")
	 * @var int
	 */
	protected $playersOnline = 0;



	/**
	 * @param int $playersTotal
	 */
	public function setPlayersTotal($playersTotal)
	{
		$this->playersTotal = intVal($playersTotal);
	}



	/**
	 * @param int $playersActive
	 */
	public function setPlayersActive($playersActive)
	{
		$this->playersActive = intVal($playersActive);
	}



	/**
	 * @param int $playersOnline
	 */
	public function setPlayersOnline($playersOnline)
	{
		$this->playersOnline = intVal($playersOnline);
	}

}
