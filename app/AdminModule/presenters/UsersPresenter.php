<?php

namespace Teddy\AdminModule\Presenters;

use Nette\Utils\ArrayHash;
use Teddy\Entities\User\User;
use Teddy\Forms\Form;



/**
 * @TODO: user info, daemon, ban?, referrals
 */
class UsersPresenter extends BasePresenter
{

	/**
	 * @var User
	 */
	protected $editedUser;



	public function renderDetail($id)
	{

	}



	public function handleDeleteCredits($id)
	{

	}



	public function createComponentCreditsForm()
	{
		$form = new Form();
		$form->addText('amount', 'Amount')
			->setType('number')
			->addRule($form::FILLED);
		$form->addText('description', 'Description');
		$form->addDate('expires', 'Expires');
		$form->onSuccess[] = function (Form $form, ArrayHash $values) {
			if ($values->amount >= 0) {
				$this->editedUser =
			}
		}
		return $form->setBootstrapRenderer();
	}

}
