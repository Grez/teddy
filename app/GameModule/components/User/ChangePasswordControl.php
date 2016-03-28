<?php

namespace Teddy\AdminModule\Components;

use Nette\Utils\ArrayHash;
use Nette\Utils\DateTime;
use Teddy;
use Teddy\Entities\User\Users;
use Teddy\Forms\Form;
use Kdyby\Doctrine\EntityManager;
use Nette\Application\UI\Control;



class ChangePasswordControl extends Control
{

	/**
	 * @var EntityManager
	 */
	private $em;

	/**
	 * @var Users
	 */
	private $users;



	public function __construct(EntityManager $em, Users $users)
	{
		$this->em = $em;
		$this->users = $users;
	}



	public function render()
	{
		$template = parent::createTemplate();
		$this['ChangePasswordFilterForm']['metrics']->setValue($this->metrics);

		$template->setFile(__DIR__ . '/ChangePassword.latte');
		$template->render();
	}



	/**
	 * @return Form
	 */
	protected function createComponentChangePasswordFilterForm()
	{
		$form = new Form();
		$form->setMethod('GET');
		$form->addDate('from', 'From', 'd.m.Y')
			->setDefaultValue($this->from)
			->setRequired('Please fill date')
			->addRule([$form['from'], 'validateDate'], 'Date is invalid');
		$form->addDate('to', 'To', 'd.m.Y')
			->setDefaultValue($this->to)
			->setRequired('Please fill date')
			->addRule([$form['to'], 'validateDate'], 'Date is invalid');
		$form->addCheckboxList('metrics', 'Metrics', $this->availableMetrics)
			->setDefaultValue(array_keys($this->availableMetrics));
		$form->addSubmit('submit');
		$form->onSuccess[] = function (Form $form, ArrayHash $values) {
			if ($values['from'] instanceof \DateTimeImmutable) {
				$this->from = \Nette\Utils\DateTime::from($values['from']->getTimestamp());
			}

			if ($values['to'] instanceof \DateTimeImmutable) {
				$this->to = \Nette\Utils\DateTime::from($values['to']->getTimestamp());
			}

			$this->metrics = ($values['metrics']) ?: array_keys($this->availableMetrics);
		};
		return $form;
	}
}



interface IChangePasswordControlFactory
{

	/** @return ChangePasswordControl */
	function create();
}
