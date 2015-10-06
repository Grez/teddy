<?php

namespace Teddy\Forms\User;

use Nette;
use Teddy\Forms\Form;



class AdminViewOnlyContainer extends Nette\Forms\Container
{

	public function __construct()
	{
		parent::__construct();

		$this->addText('last_login', 'Last login')
			->setDisabled();
		$this->addText('last_activity', 'Last activity')
			->setDisabled();
		$this->addText('registered', 'Registered')
			->setDisabled();
	}

}
