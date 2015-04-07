<?php

namespace Teddy\Forms\User;

use Nette;
use Teddy\Forms\Form;

class UserContainer extends Nette\Forms\Container
{

    public function __construct()
    {
        parent::__construct();
        $this->addHidden('id');
    }

}