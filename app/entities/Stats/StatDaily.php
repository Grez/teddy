<?php

namespace Teddy\Entities\Stats;

use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Nette;
use Teddy\Entities;
use Doctrine\ORM\Mapping as ORM;



/**
 * @ORM\MappedSuperclass()
 */
abstract class StatDaily extends \Kdyby\Doctrine\Entities\BaseEntity
{

	/**
	 * @ORM\Column(type="date", unique=true)
	 */
	protected $date;

	/**
	 * @ORM\Column(type="integer")
	 */
	protected $playersTotal = 0;

	/**
	 * @ORM\Column(type="integer")
	 */
	protected $playersActive = 0;

	/**
	 * @ORM\Column(type="integer")
	 */
	protected $playersOnline = 0;



	public function setPlayersTotal($playersTotal)
	{
		$this->playersTotal = intVal($playersTotal);
	}



	public function setPlayersActive($playersActive)
	{
		$this->playersActive = intVal($playersActive);
	}



	public function setPlayersOnline($playersOnline)
	{
		$this->playersOnline = intVal($playersOnline);
	}

}
