<?php

namespace App\Events;

use Nette;
use Kdyby;
use App\Model\Logins;
use App\Model\Users;

class LoginListener extends Nette\Object implements Kdyby\Events\Subscriber
{

    /** @var Logins */
    protected $logins;

    /** @var Users */
    protected $users;

    /** @var Nette\Security\User */
    protected $identity;


    public function __construct(Logins $logins, Users $users, Nette\Security\User $identity)
    {
        $this->logins = $logins;
        $this->users = $users;
        $this->identity = $identity;
    }

    public function onLoggedIn()
    {
        $user = $this->users->find($this->identity->id);
        $this->logins->log($user);
    }

    public function getSubscribedEvents()
    {
        return array('\Nette\Security\User::onLoggedIn');
    }

}
