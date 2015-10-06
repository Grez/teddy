<?php

namespace Teddy\Forms\User;

use Nette;
use Teddy\Forms\Form;



class AdminEditableContainer extends Nette\Forms\Container
{

	public function __construct()
	{
		parent::__construct();

		$this->addText('nick', 'Nick')
			->addRule([$this->users, 'validateNick'], 'This username is already taken.')
			->setRequired();
		$this->addText('email', 'E-mail')
			->addRule(Form::EMAIL, 'Please enter valid e-mail.')
			->setRequired();
	}

}
