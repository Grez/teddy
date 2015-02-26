<?php

namespace App\Model;

use Doctrine\ORM\Mapping as ORM;
use Nette;

/**
 * @ORM\Entity
 */
class Message extends \Kdyby\Doctrine\Entities\BaseEntity
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="to_user_id", referencedColumnName="id")
     */
    protected $to;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="from_user_id", referencedColumnName="id")
     */
    protected $from;

    /**
     * @ORM\Column(type="string")
     */
    protected $senderName = '';

    /**
     * @ORM\ManyToOne(targetEntity="Message")
     * @ORM\JoinColumn(name="conversation_id", referencedColumnName="id")
     */
    protected $conversation;

    /**
     * @ORM\Column(type="string")
     */
    protected $subject = '';

    /**
     * @ORM\Column(type="text")
     */
    protected $text = "";

    /**
     * @ORM\Column(type="smallint")
     */
    protected $type = 0;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $unread = true;

    /**
     * @ORM\Column(type="smallint")
     */
    protected $deleted = 0;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $date;


    /** Possible message deletions statuses */
    const NOT_DELETED = 0;
    const DELETED_SENDER = 1;
    const DELETED_RECIPIENT = 2;
    const DELETED_BOTH = 3;
    const DELETED_BEFORE_READING = 4; // maybe better to totally delete from db?

    /** Message types, 0-100 reserved for Teddy */
    const UNKNOWN_MSG = 0;
    const NORMAL_MSG = 1;
    const SYSTEM_MSG = 2;


    public function __construct()
    {
        $this->date = new \DateTime();
    }

    /**
     * Returns sender nick
     * @return string
     */
    public function getSenderNick()
    {
        return ($this->from instanceof User) ? $this->from->nick : $this->senderName;
    }

    /**
     * Deletes 'Re: ' from beginning
     * @param string $subject
     * @return null
     */
    public function setSubject($subject)
    {
        $this->subject = (substr($subject, 0, 4) == 'Re: ') ? substr($subject, 4) : $subject;
    }

    /**
     * Adds 'Re: ' if it is reply
     * @return string
     */
    public function getSubject()
    {
        return ($this->conversation != null) ? 'Re: ' . $this->subject : $this->subject;
    }

    /**
     * @param User $user
     * @return null
     */
    public function deleteBy(User $user)
    {
        if ($user == $this->from && $user == $this->to) {
            $this->deleted = self::DELETED_BOTH; // let's allow sending messages to yourself, because why not?
        } else if ($user == $this->from) {
            if ($this->unread) {
                $this->deleted = self::DELETED_BEFORE_READING;
            } else {
                $this->deleted = ($this->deleted == 0) ? self::DELETED_SENDER : self::DELETED_BOTH;
            }
        } else {
            $this->deleted = ($this->deleted == 0) ? self::DELETED_RECIPIENT : self::DELETED_BOTH;
        }
    }

    /**
     * Returns conversation id (ID of first message)
     * @return int
     */
    public function getConversationId()
    {
        return ($this->conversation != null) ? $this->conversation->id : $this->id;
    }

}