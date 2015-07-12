<?php

namespace Teddy\Entities\PM;

use Nette;
use Teddy\Entities;
use Kdyby\Doctrine\EntityManager;
use Teddy\Entities\User\User;
use Teddy\Entities\User\Users;


class Messages extends Entities\Manager
{

    /** @var Users */
    protected $users;


    public function __construct(EntityManager $em, Users $users)
    {
        parent::__construct($em);
        $this->users = $users;
        $this->repository = $this->em->getRepository(Message::class);
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
     * @param string (all|received|sent) $type
     * @param bool $unreadOnly
     * @return array
     */
    public function getMessagesForUser(User $user, $type = 'all', $unreadOnly = false)
    {
        $qb = $this->repository->createQueryBuilder('m');

        $allowed = array(Message::NOT_DELETED);
        if ($type == 'all') {
            $qb->where('m.to = ?1 AND m.deleted IN (?2)');
            $qb->orWhere('m.from = ?1 AND m.deleted IN (?3)');
        } else if ($type == 'received') {
            $qb->where('m.to = ?1 AND m.deleted IN (?2)');
        } else if ($type == 'sent') {
            $qb->where('m.from = ?1 AND m.deleted IN (?3)');
        }
        $qb->setParameter(1, $user);
        $qb->setParameter(2, array_merge($allowed, array(Message::DELETED_SENDER)));
        $qb->setParameter(3, array_merge($allowed, array(Message::DELETED_RECIPIENT)));

        if ($unreadOnly) {
            $qb->where('m.unread = ?4');
            $qb->setParameter(4, true);
        }

        $qb->orderBy('m.id', 'DESC');
        return $qb->getQuery()->getResult();
    }
}