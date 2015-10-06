<?php

namespace Teddy\Entities\Forum;

use Nette;
use Teddy\Entities;
use Kdyby\Doctrine\EntityManager;
use Teddy\Entities\User\User;



class Forums extends Entities\Manager
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



	public function __construct(EntityManager $em, ForumPosts $forumPosts)
	{
		parent::__construct($em);
		$this->forumsPostsRepository = $forumPosts;
		$this->repository = $this->em->getRepository(Forum::class);
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
	 *
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
