<?php

/**
 * @testCase
 */

namespace Teddy\Tests;

use Game\Entities\Forums\ForumLastVisit;
use Game\Entities\Forums\ForumPost;
use Nette;
use Teddy\Entities\Forums\AccessDenied;
use Game\Entities\Forums\Forum;
use Teddy\Entities\Forums\ForumPosts;
use Teddy\Entities\Forums\Forums;
use Game\Entities\User\Player;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/../TestCase.php';



class ForumsTest extends TestCase
{

	/**
	 * @var Forums
	 * @inject
	 */
	public $forumsRepository;

	/**
	 * @var ForumPosts
	 * @inject
	 */
	public $forumPostsRepository;



	public function setUp()
	{
		parent::setUp();
		$this->forumsRepository = $this->getService(Forums::class);
		$this->forumPostsRepository = $this->getService(ForumPosts::class);
	}



	public function testAccessDenied()
	{
		$author = new Player('mario@luigi.it');

		$writableForum = \Mockery::mock(Forum::class);
		$writableForum->shouldReceive('canWrite')->andReturn(FALSE);

		$nonWritableForum = \Mockery::mock(Forum::class);
		$nonWritableForum->shouldReceive('canWrite')->andReturn(TRUE);

		Assert::exception(function () use ($author, $writableForum) {
			$this->forumsRepository->addPost($author, $writableForum, 'Subject', 'Text');
		}, 'Teddy\Entities\Forums\AccessDenied');

		$post = $this->forumsRepository->addPost($author, $nonWritableForum, 'Subject', 'Text');
		Assert::equal($post->getAuthor(), $author);
	}



	public function testAccess()
	{
		$user = new Player('mario@plumber.it');
		$admin = new Player('luigi@plumber.it');
		$admin->setAdmin(TRUE);

		$forum1 = $this->forumsRepository->find(Forums::ADMIN_ANNOUNCEMENTS);
		$forum2 = $this->forumsRepository->find(Forums::WORLD_CHAT);
		$forum3 = $this->forumsRepository->find(Forums::HELPDESK);
		$forum4 = $this->forumsRepository->find(Forums::ALTERNATIVE);
		$forum5 = $this->forumsRepository->find(Forums::BUGS);

		Assert::true($forum1->canView($user));
		Assert::true($forum2->canView($user));
		Assert::true($forum3->canView($user));
		Assert::true($forum4->canView($user));
		Assert::true($forum5->canView($user));

		Assert::false($forum1->canWrite($user));
		Assert::true($forum2->canWrite($user));
		Assert::true($forum3->canWrite($user));
		Assert::false($forum4->canWrite($user));
		Assert::true($forum5->canWrite($user));

		Assert::true($forum1->canWrite($admin));
		Assert::true($forum2->canWrite($admin));
		Assert::true($forum3->canWrite($admin));
		Assert::true($forum4->canWrite($admin));
		Assert::true($forum5->canWrite($admin));

		// On admin annoucements can write / delete only admins
		Assert::exception(function() use ($user, $forum1) {
			$this->forumsRepository->addPost($user, $forum1, 'Subject', 'Text');
		}, AccessDenied::class);

		$post = $this->forumsRepository->addPost($admin, $forum1, 'Subject', 'Text');
		Assert::true($post->canDelete($admin));

		// User can delete his post, admin can delete your post, but user can't delete someone elses
		$post2 = $this->forumsRepository->addPost($user, $forum2, 'Subject', 'Text');
		Assert::true($post2->canDelete($user));
		Assert::true($post2->canDelete($admin));
		Assert::false($post->canDelete($user));

		// Check all forums he can see
		$forums = $this->forumsRepository->getForumsForUser($user);
		Assert::notEqual(0, count($forums));
		foreach ($forums as $forum) {
			if ($forum->canWrite($user)) {
				$post = $this->forumsRepository->addPost($user, $forum, 'Subject', 'Text');
				Assert::true($post->canDelete($user));

			} else {
				Assert::exception(function() use ($user, $forum) {
					$this->forumsRepository->addPost($user, $forum, 'Subject', 'Text');
				}, AccessDenied::class);
			}
		}
	}



	public function testUnreadPosts()
	{
		$mario = new Player('mario@plumber.it');
		$luigi = new Player('luigi@plumber.it');
		$quattro = new Player('quattro@plumber.it');
		$this->getEm()->persist([$mario, $luigi, $quattro])->flush();

		/** @var Forum $worldChat */
		$worldChat = $this->getEm()->find(Forum::class, Forums::WORLD_CHAT);

		// There are no posts
		$forums = $this->forumsRepository->getForumsWithUnreadPosts($mario);
		foreach ($forums as $forum) {
			Assert::equal(0, $forum->getUnreadPostsCountForUser($mario));
		}
		$forums = $this->forumsRepository->getForumsWithUnreadPosts($quattro);
		foreach ($forums as $forum) {
			Assert::equal(0, $forum->getUnreadPostsCountForUser($quattro));
		}

		// Let's add two posts (one deleted)
		$post = new ForumPost($luigi, $worldChat, 'Subject', 'Text');
		$deletedPost = new ForumPost($luigi, $worldChat, 'Subject', 'Text');
		$deletedPost->delete($luigi);
		$this->getEm()->persist([$post, $deletedPost])->flush();

		$lastVisit = new ForumLastVisit($quattro, $forums[1]);
		$lastVisit->setLastVisitAt((new \DateTime())->modify('+ 10 seconds'));

		$this->getEm()->persist($lastVisit)->flush();

		// Mario sees has one new post, Quattro doesn't
		$forums = $this->forumsRepository->getForumsWithUnreadPosts($mario);
		foreach ($forums as $forum) {
			$posts = $post->getForum() === $forum ? 1 : 0;
			Assert::equal($posts, $forum->getUnreadPostsCountForUser($mario));
		}
		$forums = $this->forumsRepository->getForumsWithUnreadPosts($quattro);
		foreach ($forums as $forum) {
			Assert::equal(0, $forum->getUnreadPostsCountForUser($quattro));
		}

		// Mario checks forum, now he has no new posts
		$this->forumsRepository->updateLastVisit($mario, $worldChat);
		$this->getEm()->flush();
		$forums = $this->forumsRepository->getForumsWithUnreadPosts($mario);
		foreach ($forums as $forum) {
			Assert::equal(0, $forum->getUnreadPostsCountForUser($mario));
		}
	}

}

$test = new ForumsTest();
$test->run();
