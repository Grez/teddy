<?php

namespace Teddy\Entities\Forums;

use Nette;
use Teddy\Entities;
use Doctrine\ORM\Mapping as ORM;
use Game\Entities\User\Player;



/**
 * @ORM\MappedSuperclass()
 */
abstract class ForumPost extends \Kdyby\Doctrine\Entities\BaseEntity
{

	/**
	 * @ORM\ManyToOne(targetEntity="\Game\Entities\Forums\Forum", inversedBy="posts")
	 * @ORM\JoinColumn(name="forum_id", referencedColumnName="id")
	 * @var Forum
	 */
	protected $forum;

	/**
	 * @ORM\ManyToOne(targetEntity="\Game\Entities\User\Player")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 * @var Player
	 */
	protected $author;

	/**
	 * @ORM\ManyToOne(targetEntity="\Game\Entities\Forums\ForumPost")
	 * @ORM\JoinColumn(name="conversation_id", referencedColumnName="id")
	 * @var ForumPost
	 */
	protected $conversation;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	protected $subject;

	/**
	 * @ORM\Column(type="text")
	 * @var string
	 */
	protected $text;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 * @var \DateTime
	 */
	protected $deletedAt;

	/**
	 * @ORM\ManyToOne(targetEntity="\Game\Entities\User\Player")
	 * @ORM\JoinColumn(name="deleted_by", referencedColumnName="id")
	 * @var Player
	 */
	protected $deletedBy;

	/**
	 * @ORM\Column(type="datetime")
	 * @var \DateTime
	 */
	protected $createdAt;

	/**
	 * @ORM\Column(type="smallint")
	 * @var int
	 */
	protected $type = 0;

	/** Post types, 0-100 reserved for Teddy */
	const NORMAL_POST = 0;
	const STICKY_POST = 1;



	public function __construct(Player $author, Forum $forum, $subject, $text)
	{
		if (!$forum->canWrite($author)) {
			throw new AccessDenied('You can\'t write on this forum');
		}

		$this->author = $author;
		$this->subject = $subject;
		$this->text = $text;
		$this->createdAt = new \DateTime();
		$this->forum = $forum;
	}



	/**
	 * @param Player $user
	 * @return NULL
	 */
	public function delete(Player $user)
	{
		$this->deletedAt = new \DateTime();
		$this->deletedBy = $user;
	}



	/**
	 * @param Player $user
	 * @return bool
	 */
	public function canDelete(Player $user)
	{
		if ($user->isAdmin()) {
			return TRUE;
		}

		if ($this->author === $user) {
			return TRUE;
		}

		return FALSE;
	}



	/**
	 * @return bool
	 */
	public function isDeleted()
	{
		return $this->deletedAt !== NULL && $this->deletedAt <= new Nette\Utils\DateTime();
	}



	/**
	 * Returns conversation id (ID of first message)
	 *
	 * @return int
	 */
	public function getConversationId()
	{
		return $this->conversation ? $this->conversation->id : $this->id;
	}



	/**
	 * @return Player
	 */
	public function getAuthor()
	{
		return $this->author;
	}



	/**
	 * @param ForumPost $conversation
	 * @return ForumPost
	 */
	public function setConversation(ForumPost $conversation = NULL)
	{
		$this->conversation = $conversation;
		return $this;
	}



	/**
	 * @param Player $user
	 * @return bool
	 */
	public function isUnreadBy(Player $user)
	{
		$lastVisitBy = $this->forum->getLastVisitBy($user);
		if (!$lastVisitBy) {
			return FALSE;
		}

		return $lastVisitBy->getLastVisitAt() <= $this->createdAt;
	}



	/**
	 * @return Forum
	 */
	public function getForum()
	{
		return $this->forum;
	}

}
