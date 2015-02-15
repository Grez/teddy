<?php

namespace App\Model;

use Nette;

class Logins extends Manager
{

    /**
     * @param User
     * @return NULL
     */
    public function log(User $user)
    {
        $login = new Login();
        $login->setUser($user);

        $this->em->persist($login);
        $this->em->flush();
    }
}