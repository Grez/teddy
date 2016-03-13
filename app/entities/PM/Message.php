<?php

namespace Teddy\Entities\PM;

use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Nette;
use Teddy\Entities;
use Doctrine\ORM\Mapping as ORM;
use Teddy\Entities\User\User;
use Teddy\User\InvalidArgumentException;


/**
 * @ORM\Entity()
 * @ORM\Table(indexes={
 *   @ORM\Index(columns={"deleted_by_sender"}),
 *   @ORM\Index(columns={"deleted_by_recipient"})
 * })
 */
class Message extends \Kdyby\Doctrine\Entities\BaseEntity
{

	use Identifier;

	/**
	 * @ORM\ManyToOne(targetEntity="Teddy\Entities\User\User")
	 * @ORM\JoinColumn(name="to_user_id", referencedColumnName="id")
	 * @var User
	 */
	protected $to;

	/**
	 * @ORM\ManyToOne(targetEntity="Teddy\Entities\User\User")
	 * @ORM\JoinColumn(name="from_user_id", referencedColumnName="id")
	 * @var User
	 */
	protected $from;

	/**
	 * @ORM\ManyToOne(targetEntity="Message")
	 * @ORM\JoinColumn(name="conversation_id", referencedColumnName="id")
	 * @var Message
	 */
	protected $conversation;

	/**
	 * @ORM\Column(type="string", nullable=FALSE)
	 * @var string
	 */
	protected $subject;

	/**
	 * @ORM\Column(type="text", nullable=FALSE)
	 * @var string
	 */
	protected $text;

	/**
	 * @ORM\Column(type="smallint")
	 * @var int
	 */
	protected $type = 0;

	/**
	 * @ORM\Column(type="boolean", nullable=FALSE)
	 * @var bool
	 */
	protected $unread = TRUE;

	/**
	 * @ORM\Column(type="boolean", nullable=FALSE)
	 * @var bool
	 */
	protected $deletedBySender = FALSE;

	/**
	 * @ORM\Column(type="boolean", nullable=FALSE)
	 * @var bool
	 */
	protected $deletedByRecipient = FALSE;

	/**
	 * @ORM\Column(type="datetime")
	 * @var \DateTime
	 * Generated in __construct()
	 */
	protected $sentAt;

	/** Message types, 0-100 reserved for Teddy */
	const UNKNOWN_MSG = 0;
	const NORMAL_MSG = 1;
	const SYSTEM_MSG = 2;


	public function __construct(User $to, User $from, $subject = '', $text = '', $type = self::NORMAL_MSG)
	{
		$this->sentAt = new \DateTime();
		$this->to = $to;
		$this->from = $from;
		$this->setSubject($subject);
		$this->text = $text;
		$this->type = $type;
	}


	/**
	 * Deletes 'Re: ' from beginning
	 *
	 * @param string $subject
	 * @return NULL
	 */
	public function setSubject($subject)
	{
		$this->subject = substr($subject, 0, 4) === 'Re: ' ? substr($subject, 4) : $subject;
	}


	/**
	 * Adds 'Re: ' if it is reply
	 *
	 * @return string
	 */
	public function getSubject()
	{
		return $this->conversation ? 'Re: ' . $this->subject : $this->subject;
	}


	/**
	 * @param User $user
	 * @throws InvalidArgumentException
	 */
	public function deleteBy(User $user)
	{
		if ($user !== $this->from && $user !== $this->to) {
			throw new InvalidArgumentException('This user can\'t delete this message');
		}

		if ($user === $this->from) {
			$this->deletedBySender = TRUE;
		}

		if ($user === $this->to) {
			$this->deletedByRecipient = TRUE;
		}
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
	 * @param User $user
	 * @return bool
	 */
	public function isReadableByUser(User $user)
	{
		if ($user !== $this->to && $user !== $this->from) {
			return FALSE;
		}

		if ($user === $this->to && $this->deletedByRecipient) {
			return FALSE;
		}

		if ($user === $this->from && $this->deletedBySender) {
			return FALSE;
		}

		return TRUE;
	}


	/**
	 * @return User
	 */
	public function getTo()
	{
		return $this->to;
	}


	/**
	 * @return User
	 */
	public function getFrom()
	{
		return $this->from;
	}


	/**
	 * @return $this
	 */
	public function markRead()
	{
		$this->unread = FALSE;
		return $this;
	}


	/**
	 * @return $this
	 */
	public function markUnread()
	{
		$this->unread = TRUE;
		return $this;
	}

}
