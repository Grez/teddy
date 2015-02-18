<?php

namespace App\Security;

use Nette;

class User extends Nette\Security\User
{

    /**
     * Conducts the authentication process.
     * @param  string $login
     * @param  string $password
     * @return null
     * @throws AuthenticationException if authentication was not successful
     */
    public function login($login = NULL, $password = NULL)
    {
        $this->logout(TRUE);
        $id = $this->getAuthenticator()->authenticate(func_get_args());
        $this->storage->setIdentity($id);
        $this->storage->setAuthenticated(TRUE);
        $this->onLoggedIn($this, $login);
    }

}