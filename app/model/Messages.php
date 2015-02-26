<?php

namespace App\Model;

use Kdyby\Doctrine\EntityDao;
use Kdyby\Doctrine\EntityManager;
use Nette;
use App\Model\Users;
use App\Model\User;

class Messages extends Manager
{

    /** @var Users */
    protected $users;


    public function __construct(EntityDao $dao, EntityManager $em, Users $users)
    {
        parent::__construct($dao, $em);
        $this->users = $users;
    }

    /**
     * Sends new message
     * @param User $from
     * @param User $to
     * @param string $subject
     * @param string $text
     * @param int $conversation
     * @param int $type
     * @return NULL
     */
    public function createMessage(User $from, User $to, $subject = '', $text = '', $conversation = 0, $type = Message::NORMAL_MSG)
    {
        $msg = new Message();
        $msg->setFrom($from);
        $msg->setTo($to);
        $msg->setSubject($subject);
        $msg->setText($text);
        $msg->setType($type);
        if ($conversation > 0) {
            $msg->setConversation($this->find($conversation));
        }

        $this->em->persist($msg);
        $this->em->flush();
    }

    /**
     * @param User $user
     * @param string (received|sent) $type
     * @param bool $unreadOnly
     * @return array
     */
    public function getMessagesForUser(User $user, $type = 'received', $unreadOnly = false)
    {
        $allowed = array(Message::NOT_DELETED);
        if ($type == 'received') {
            $criteria['to'] = $user;
            $allowed[] = Message::DELETED_SENDER;
        } else if ($type == 'sent') {
            $criteria['from'] = $user;
            $allowed[] = Message::DELETED_RECIPIENT;
        }
        $criteria = array('deleted' => $allowed);

        if ($unreadOnly) {
            $criteria['unread'] = true;
        }

        $order = array(
            'date' => 'DESC',
            'id' => 'DESC',
        );
        return $this->findBy($criteria, $order);
    }
}