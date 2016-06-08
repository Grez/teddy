<?php

namespace Teddy\Entities\Forums;

use Nette;
use Teddy\Entities;
use Doctrine\ORM\Mapping as ORM;
use Teddy\Entities\User\Player;



/**
 * @ORM\MappedSuperclass()
 */
abstract class ForumLastVisit extends \Kdyby\Doctrine\Entities\BaseEntity
{

	/**
	 * @ORM\ManyToOne(targetEntity="\Game\Entities\User\Player")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 * @var Player
	 */
	protected $user;

	/**
	 * @ORM\ManyToOne(targetEntity="\Game\Entities\Forums\Forum", inversedBy="lastVisits")
	 * @ORM\JoinColumn(name="forum_id", referencedColumnName="id")
	 * @var Forum
	 */
	protected $forum;

	/**
	 * @ORM\Column(type="datetime")
	 * @var \DateTime
	 */
	protected $lastVisitAt;



	public function __construct(Player $user, Forum $forum)
	{
		$this->user = $user;
		$this->forum = $forum;
		$this->lastVisitAt = new \DateTime();
	}



	/**
	 * @return ForumLastVisit
	 */
	public function updateLastVisitAt()
	{
		$this->lastVisitAt = new \DateTime();
		return $this;
	}



	/**
	 * @return \DateTime
	 */
	public function getLastVisitAt()
	{
		return $this->lastVisitAt;
	}

}
