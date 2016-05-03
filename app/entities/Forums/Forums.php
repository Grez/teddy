<?php

namespace Teddy\Entities\Forums;

use Nette;
use Teddy\Entities;
use Kdyby\Doctrine\EntityManager;
use Teddy\Entities\User\User;



class Forums extends Entities\Manager
{

	/**
	 * @var ForumPosts
	 */
	protected $forumsPostsRepository;

	/** Forums, 0-50 reserved for Teddy */
	const ADMIN_ANNOUNCEMENTS = 1;
	const WORLD_CHAT = 2;
	const ADMINS = 3;
	const HELPDESK = 4;
	const ALTERNATIVE = 5;
	const BUGS = 6;



	public function __construct(EntityManager $em, ForumPosts $forumPosts)
	{
		parent::__construct($em);
		$this->forumsPostsRepository = $forumPosts;
		$this->repository = $this->em->getRepository(\Game\Entities\Forums\Forum::class);
	}



	/**
	 * @return Forum[]
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
	 * @return Forum[]
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



	/**
	 * @param User $author
	 * @param \Game\Entities\Forums\Forum $forum
	 * @param string $subject
	 * @param string $text
	 * @param \Game\Entities\Forums\ForumPost|NULL $conversation
	 * @return ForumPost
	 * @throw AccessDenied
	 */
	public function addPost(User $author, \Game\Entities\Forums\Forum $forum, $subject = '', $text = '', \Game\Entities\Forums\ForumPost $conversation = NULL)
	{
		$post = new \Game\Entities\Forums\ForumPost($author, $forum, $subject, $text);
		$post->setConversation($conversation);
		$this->em->persist($post);

		return $post;
	}



	/**
	 * @param User $user
	 * @param Forum $forum
	 */
	public function updateLastVisit(User $user, Forum $forum)
	{
		$lastVisit = $this->em->getRepository(\Game\Entities\Forums\ForumLastVisit::class)->findOneBy([
			'user' => $user,
			'forum' => $forum,
		]);
		$lastVisit = $lastVisit ?: new \Game\Entities\Forums\ForumLastVisit($user, $forum);
		$lastVisit->updateLastVisitAt();
		$this->em->persist($lastVisit);
	}

}