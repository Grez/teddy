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
 *   @ORM\Index(columns={"deleted_at"})
 * })
 */
class ForumPost extends \Kdyby\Doctrine\Entities\BaseEntity
{

	use Identifier;

	/**
	 * @ORM\ManyToOne(targetEntity="Forum", inversedBy="posts")
	 * @ORM\JoinColumn(name="forum_id", referencedColumnName="id")
	 * @var Forum
	 */
	protected $forum;

	/**
	 * @ORM\ManyToOne(targetEntity="Teddy\Entities\User\User")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 * @var User
	 */
	protected $author;

	/**
	 * @ORM\ManyToOne(targetEntity="ForumPost")
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
	 * @ORM\ManyToOne(targetEntity="Teddy\Entities\User\User")
	 * @ORM\JoinColumn(name="deleted_by", referencedColumnName="id")
	 * @var User
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



	public function __construct(User $author, Forum $forum, $subject, $text)
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
	 * @param User $user
	 * @return NULL
	 */
	public function delete(User $user)
	{
		$this->deletedAt = new \DateTime();
		$this->deletedBy = $user;
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
	 * @return User
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

}
