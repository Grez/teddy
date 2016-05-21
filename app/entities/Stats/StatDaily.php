<?php

namespace Teddy\Entities\Stats;

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
	 * @var \DateTime
	 */
	protected $date;

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



	public function __construct(\DateTime $date)
	{
		$this->date = $date;
	}



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
