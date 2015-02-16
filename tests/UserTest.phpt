<?php

namespace Test;

use Nette,
	Tester,
	Tester\Assert;
use App\Model\User;

$container = require __DIR__ . '/bootstrap.php';


class UserTest extends Tester\TestCase
{

    function testMailHiding()
	{
        $user = new User();
        $user->setEmail('john.doe@gmail.com');
        Assert::equal('john.doe@gmail.com', $user->getEmail());
        Assert::equal('j******e@gmail.com', $user->getEmail(true));

        $user->setEmail('j@doe.com');
        Assert::equal('j@doe.com', $user->getEmail());
        Assert::equal('j@doe.com', $user->getEmail(true));

        $user->setEmail('jd@johndoe.com');
        Assert::equal('jd@johndoe.com', $user->getEmail());
        Assert::equal('jd@johndoe.com', $user->getEmail(true));
	}

}


$test = new UserTest($container);
$test->run();
