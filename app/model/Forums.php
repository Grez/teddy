<?php

namespace App\Model;

use Kdyby\Doctrine\EntityDao;
use Kdyby\Doctrine\EntityManager;
use Nette;

class Forums extends Manager
{

    /** @var ForumPosts */
    protected $forumsPostsRepository;

    /** Forums, 0-50 reserved for Teddy */
    const ADMIN_ANNOUNCEMENTS = 1;
    const WORLD_CHAT = 2;
    const ADMINS = 3;
    const HELPDESK = 4;
    const ALTERNATIV = 5;
    const ANTIMULTI = 6;
    const BUGS = 7;


    public function __construct(EntityDao $dao, EntityManager $em, ForumPosts $forumPosts)
    {
        parent::__construct($dao, $em);
        $this->forumsPostsRepository = $forumPosts;
    }

    /**
     * @return array(Forum)
     */
    public function getForums()
    {
        return $this->findBy([]);
    }

    /**
     * Returns list of Forums for User
     * @TODO: cache?
     * @param User $user
     * @return array(id => name)
     */
    public function getForumsForUser(User $user)
    {
        $forums = $this->getForums();
        foreach ($forums as $id => $forum) {
            if (!$forum->canView($user)) {
                unset($forums[$id]);
            }
        }
        return $forums;
    }

    public function addPost(User $user, Forum $forum, $subject = '', $text = '', $conversation = 0)
    {
        $post = new ForumPost();
        $post->setAuthor($user);
        $post->setForum($this->find($forum));
        $post->setSubject($subject);
        $post->setText($text);
        if ($conversation > 0) {
            $post->setConversation($this->forumsPostsRepository->find($conversation));
        }

        $this->em->persist($post);
        $this->em->flush();
    }

}