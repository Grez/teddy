<?php

namespace Teddy\Entities\Forum;

use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Nette;
use Teddy\Entities;
use Doctrine\ORM\Mapping as ORM;
use Teddy\Entities\User\User;



/**
 * @ORM\Entity()
 * @ORM\Table(indexes={
 *   @ORM\Index(columns={"deleted"})
 * })
 */
class ForumPost extends \Kdyby\Doctrine\Entities\BaseEntity
{

	use Identifier;

	/**
	 * @ORM\ManyToOne(targetEntity="Forum", inversedBy="forum")
	 * @ORM\JoinColumn(name="forum_id", referencedColumnName="id")
	 */
	protected $forum;

	/**
	 * @ORM\ManyToOne(targetEntity="Teddy\Entities\User\User")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 */
	protected $author;

	/**
	 * @ORM\ManyToOne(targetEntity="Forum")
	 * @ORM\JoinColumn(name="conversation_id", referencedColumnName="id")
	 */
	protected $conversation;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $subject;

	/**
	 * @ORM\Column(type="text")
	 */
	protected $text = "";

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	protected $deleted;

	/**
	 * @ORM\ManyToOne(targetEntity="Teddy\Entities\User\User")
	 * @ORM\JoinColumn(name="deleted_by", referencedColumnName="id")
	 */
	protected $deleteBy;

	/**
	 * @ORM\Column(type="datetime")
	 */
	protected $date;

	/**
	 * @ORM\Column(type="smallint")
	 */
	protected $type = 0;

	/** Post types, 0-100 reserved for Teddy */
	const NORMAL_POST = 0;
	const STICKY_POST = 1;



	public function __construct()
	{
		$this->date = new \DateTime();
	}



	/**
	 * @param User $user
	 * @return null
	 */
	public function delete(User $user)
	{
		$this->deleted = new \DateTime();
		$this->deletedBy = $user;
	}



	public function isDeleted()
	{
		return ($this->deleted !== NULL && $this->deleted <= new Nette\Utils\DateTime());
	}



	/**
	 * Returns conversation id (ID of first message)
	 *
	 * @return int
	 */
	public function getConversationId()
	{
		return ($this->conversation != NULL) ? $this->conversation->id : $this->id;
	}

}
