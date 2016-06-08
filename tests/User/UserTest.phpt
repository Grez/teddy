<?php

/**
 * @testCase Teddy\Tests\UserTest
 */

namespace Teddy\Tests;

use Nette;
use Teddy\Entities\User\Users;
use Tester;
use Tester\Assert;
use Game\Entities\User\Player;

require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/../TestCase.php';



class UserTest extends TestCase
{

	public function testMailHiding()
	{
		$user = new Player('mario.luigi@quattro.formaggi.it');
		$user->setEmail('john.doe@gmail.com');
		Assert::equal('john.doe@gmail.com', $user->getEmail());
		Assert::equal('j******e@gmail.com', $user->getAnonymizedEmail());

		$user->setEmail('j@doe.com');
		Assert::equal('j@doe.com', $user->getEmail());
		Assert::equal('j@doe.com', $user->getAnonymizedEmail());

		$user->setEmail('jd@johndoe.com');
		Assert::equal('jd@johndoe.com', $user->getEmail());
		Assert::equal('jd@johndoe.com', $user->getAnonymizedEmail());
	}



	public function testDeletingAndReactivating()
	{
		$mario = new Player('mario.luigi@quattro.formaggi.it', 'Mario Luigi');
		$trollMario = new Player('mario.luigi@quattro.formaggi.it', 'Mario Luigi (deleted #0)');
		$this->getEm()->persist([$mario, $trollMario]);
		$this->getEm()->flush();

		/** @var Users $users */
		$users = $this->getService(Users::class);
		$users->markDeleted($mario);
		Assert::true($mario->isDeleted());
		Assert::equal('Mario Luigi (deleted #1)', $mario->getNick());

		$newMario = new Player('mario.luigi@quattro.formaggi.it', 'Mario Luigi');
		$this->getEm()->persist($newMario);
		$this->getEm()->flush();

		$ultraTroll = new Player('mario.luigi@quattro.formaggi.it', 'Mario Luigi (reactivated #1)');
		$this->getEm()->persist($ultraTroll);
		$this->getEm()->flush();

		$users->reactivate($mario);
		Assert::false($mario->isDeleted());
		Assert::equal('Mario Luigi (reactivated #2)', $mario->getNick());

		// Check behaviour for normal User
		$normalUser = new Player('normal@user.cz', 'Normal User');
		$this->getEm()->persist($normalUser);
		$this->getEm()->flush();

		$users->markDeleted($normalUser);
		Assert::true($normalUser->isDeleted());
		Assert::equal('Normal User (deleted #0)', $normalUser->getNick());

		$users->reactivate($normalUser);
		Assert::false($normalUser->isDeleted());
		Assert::equal('Normal User', $normalUser->getNick());
	}

}

$test = new UserTest();
$test->run();
