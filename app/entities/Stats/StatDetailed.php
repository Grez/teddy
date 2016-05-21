<?php

namespace Teddy\Entities\Stats;

use Nette;
use Teddy\Entities;
use Doctrine\ORM\Mapping as ORM;



/**
 * @ORM\MappedSuperclass()
 */
abstract class StatDetailed extends \Kdyby\Doctrine\Entities\BaseEntity
{

	/**
	 * @ORM\Column(type="date")
	 * @var \DateTime
	 * Generated in __construct()
	 */
	protected $date;

	/**
	 * @ORM\Column(type="time")
	 * @var \DateTime
	 * Generated in __construct()
	 */
	protected $time;

	/**
	 * @ORM\Column(type="integer")
	 * @var int
	 */
	protected $playersTotal;

	/**
	 * @ORM\Column(type="integer")
	 * @var int
	 */
	protected $playersActive;

	/**
	 * @ORM\Column(type="integer")
	 * @var int
	 */
	protected $playersOnline;

	/**
	 * @ORM\Column(type="float", nullable=true)
	 * @var float
	 */
	protected $load1;

	/**
	 * @ORM\Column(type="float", nullable=true)
	 * @var float
	 */
	protected $load5;

	/**
	 * @ORM\Column(type="float", nullable=true)
	 * @var float
	 */
	protected $load15;



	public function __construct()
	{
		$this->date = new \DateTime();
		$this->time = new \DateTime();
	}

}
