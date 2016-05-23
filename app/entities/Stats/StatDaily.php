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

	use \Game\Entities\Stats\StatsTrait;

	/**
	 * @ORM\Column(type="date", unique=true)
	 * @var \DateTime
	 */
	protected $date;

	/**
	 * @ORM\Column(type="float", nullable=true)
	 * @var float
	 */
	protected $avgLoad;

	/**
	 * @ORM\Column(type="float", nullable=true)
	 * @var float
	 */
	protected $maxLoad5;

	/**
	 * @ORM\Column(type="float", nullable=true)
	 * @var float
	 */
	protected $maxLoad15;



	public function __construct(\DateTime $date)
	{
		$this->date = $date;
	}



	/**
	 * @param float $avgLoad
	 * @return StatDaily
	 */
	public function setAvgLoad($avgLoad)
	{
		$this->avgLoad = $avgLoad;
		return $this;
	}



	/**
	 * @param float $maxLoad5
	 * @return StatDaily
	 */
	public function setMaxLoad5($maxLoad5)
	{
		$this->maxLoad5 = $maxLoad5;
		return $this;
	}



	/**
	 * @param float $maxLoad15
	 * @return StatDaily
	 */
	public function setMaxLoad15($maxLoad15)
	{
		$this->maxLoad15 = $maxLoad15;
		return $this;
	}

}
