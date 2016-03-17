<?php

/**
 * @testCase
 */

namespace Teddy\Tests;

use Nette;
use Teddy\Entities\Forum\Forum;
use Teddy\Entities\Forum\ForumPosts;
use Teddy\Entities\Forum\Forums;
use Teddy\Entities\User\User;
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
		$author = new User('mario@luigi.it');

		$writableForum = \Mockery::mock(Forum::class);
		$writableForum->shouldReceive('canWrite')->andReturn(FALSE);

		$nonWritableForum = \Mockery::mock(Forum::class);
		$nonWritableForum->shouldReceive('canWrite')->andReturn(TRUE);

		Assert::exception(function () use ($author, $writableForum) {
			$this->forumsRepository->addPost($author, $writableForum, 'Subject', 'Text');
		}, 'Teddy\Entities\Forum\AccessDenied');

		$post = $this->forumsRepository->addPost($author, $nonWritableForum, 'Subject', 'Text');
		Assert::equal($post->getAuthor(), $author);
	}



//	public function testReadability()
//	{
		// nemůžu číst fóra, kam nemám přístup
		// smazané příspěvky se nefetchnou
//	}

}

$test = new ForumsTest();
$test->run();
