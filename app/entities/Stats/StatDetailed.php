<?php

namespace Teddy\Entities\Stats;

use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Nette;
use Teddy\Entities;
use Doctrine\ORM\Mapping as ORM;



/**
 * @ORM\Entity()
 * @ORM\Table(uniqueConstraints={
 *   @ORM\UniqueConstraint(columns={"date", "time"})
 * })
 */
class StatDetailed extends \Kdyby\Doctrine\Entities\BaseEntity
{

	use Identifier;

	/**
	 * @ORM\Column(type="date")
	 * Generated in __construct()
	 */
	protected $date;

	/**
	 * @ORM\Column(type="time")
	 * Generated in __construct()
	 */
	protected $time;

	/**
	 * @ORM\Column(type="integer")
	 */
	protected $playersTotal;

	/**
	 * @ORM\Column(type="integer")
	 */
	protected $playersActive;

	/**
	 * @ORM\Column(type="integer")
	 */
	protected $playersOnline;

	/**
	 * @ORM\Column(type="float", nullable=true)
	 */
	protected $load1;

	/**
	 * @ORM\Column(type="float", nullable=true)
	 */
	protected $load5;

	/**
	 * @ORM\Column(type="float", nullable=true)
	 */
	protected $load15;



	public function __construct()
	{
		$this->date = new \DateTime();
		$this->time = new \DateTime();
	}

}
