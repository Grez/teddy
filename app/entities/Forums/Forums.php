<?php

namespace Teddy\Entities\Forums;

use Nette;
use Teddy\Entities;
use Kdyby\Doctrine\EntityManager;
use Game\Entities\User\Player;



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
	 * @param Player $user
	 * @return Forum[]
	 */
	public function getForumsForUser(Player $user)
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
	 * Returns User's Forums with unread posts count
	 *
	 * @param Player $user
	 * @return \Game\Entities\Forums\Forum[]
	 */
	public function getForumsWithUnreadPosts(Player $user)
	{
		$forums = $this->getForumsForUser($user);
		$query = (new ForumsQuery())
			->withUnreadPostsCount($user)
			->onlyForums($forums);

		/** @var \Game\Entities\Forums\Forum[] $forums */
		$result = $this->repository->fetch($query);

		$forums = [];
		foreach ($result as $item) {
			/** @var \Game\Entities\Forums\Forum $forum */
			$forum = $item[0];
			$forum->setUnreadPostsCountForUser($user, $item['unread_posts_count']);
			$forums[] = $forum;
		}

		return $forums;
	}



	/**
	 * @param Player $author
	 * @param \Game\Entities\Forums\Forum $forum
	 * @param string $subject
	 * @param string $text
	 * @param \Game\Entities\Forums\ForumPost|NULL $conversation
	 * @return ForumPost
	 * @throw AccessDenied
	 */
	public function addPost(Player $author, \Game\Entities\Forums\Forum $forum, $subject = '', $text = '', \Game\Entities\Forums\ForumPost $conversation = NULL)
	{
		$post = new \Game\Entities\Forums\ForumPost($author, $forum, $subject, $text);
		$post->setConversation($conversation);
		$this->em->persist($post);

		return $post;
	}



	/**
	 * @non-flushing
	 *
	 * @param Player $user
	 * @param \Game\Entities\Forums\Forum $forum
	 * @return \Game\Entities\Forums\ForumLastVisit
	 */
	public function updateLastVisit(Player $user, \Game\Entities\Forums\Forum $forum)
	{
		$lastVisit = $forum->getLastVisitBy($user);
		$lastVisit = $lastVisit ?: new \Game\Entities\Forums\ForumLastVisit($user, $forum);
		$lastVisit->updateLastVisitAt();
		$forum->addLastVisitBy($lastVisit);
		$this->em->persist($lastVisit);
		return $lastVisit;
	}

}
