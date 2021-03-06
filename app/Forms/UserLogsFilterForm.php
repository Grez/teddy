<?php

namespace Teddy\Forms;

use Teddy;
use Nette;
use Kdyby;



class UserLogsFilterForm extends Nette\Application\UI\Control
{

	/**
	 * @var int|NULL @persistent
	 */
	public $userId;

	/**
	 * @var Kdyby\Doctrine\EntityManager
	 */
	protected $em;

	/**
	 * @var Teddy\Entities\User\Users
	 */
	protected $users;



	public function __construct(Kdyby\Doctrine\EntityManager $em, Teddy\Entities\User\Users $users)
	{
		$this->em = $em;
		$this->users = $users;
	}



	protected function attached($parent)
	{
		parent::attached($parent);

		if ($parent instanceof Nette\Application\IPresenter) {
			if ($this->userId !== NULL) {
				$this['userLogsFilterForm-userId']->setDefaultValue($this->userId);
			}
		}
	}



	protected function createComponentUserLogsFilterForm()
	{
		$form = new Form();
		$form->setMethod('GET');
		$form->addSelect('userId', 'Admin')
			->setItems([0 => 'All'] + $this->users->findPairs(['admin' => TRUE], "nick", [], "id"));

		$form->addSubmit('send', 'Filter');

		$form->onSuccess[] = function ($_, $values) {
			$this->redirect('this', [
				'userId' => $values['userId'],
			]);
		};

		return $form;
	}



	public function render()
	{
		$this['userLogsFilterForm']->render();
	}

}



interface IUserLogsFilterFormFactory
{

	/** @return UserLogsFilterForm */
	function create();
}
