<?php

namespace App\Events;

use Nette;
use Kdyby;
use App\Model\Bans;
use App\Model\Login;
use App\Model\Logins;
use App\Model\Users;

class LoginListener extends Nette\Object implements Kdyby\Events\Subscriber
{

    /** @var Logins */
    protected $logins;

    /** @var Users */
    protected $users;

    /** @var Bans */
    protected $bans;


    public function __construct(Logins $logins, Users $users, Bans $bans)
    {
        $this->logins = $logins;
        $this->users = $users;
        $this->bans = $bans;
    }

    public function onLoggedIn($identity, $login)
    {
        $user = $this->users->find($identity->id);
        $this->logins->log($user, $login);
    }

    public function onWrongPassword($login)
    {
        $this->logins->log(NULL, $login, Login::WRONG_PASSWORD);
        if($this->logins->isTryingToHack($_SERVER['REMOTE_ADDR'])) {
            $this->bans->ban($_SERVER['REMOTE_ADDR'], 'Too many attempts to login', 1/24);
        }
    }

    public function onWrongLogin($login)
    {
        $this->logins->log(NULL, $login, Login::WRONG_LOGIN);
    }

    public function getSubscribedEvents()
    {
        return array(
            '\App\Security\User::onLoggedIn',
            '\App\Model\Users::onWrongLogin',
            '\App\Model\Users::onWrongPassword',
        );
    }

}
