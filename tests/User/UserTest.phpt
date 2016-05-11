<?php

/**
 * @testCase Teddy\Tests\UserTest
 */

namespace Teddy\Tests;

use Nette;
use Tester;
use Tester\Assert;
use Game\Entities\User\User;

require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/../TestCase.php';



class UserTest extends TestCase
{

	public function testMailHiding()
	{
		$user = new User('mario.luigi@quattro.formaggi.it');
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

}

$test = new UserTest();
$test->run();
