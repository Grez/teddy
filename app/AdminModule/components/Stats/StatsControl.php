<?php

namespace Teddy\AdminModule\Components;

use Nette\Utils\ArrayHash;
use Nette\Utils\DateTime;
use Teddy;
use Teddy\Entities\Stats\StatDailyManager;
use Teddy\Forms\Form;
use Kdyby\Doctrine\EntityManager;
use Nette\Application\UI\Control;



class StatsControl extends Control
{

	/**
	 * @var \DateTime|null
	 */
	protected $from = NULL;

	/**
	 * @var \DateTime|null
	 */
	protected $to = NULL;

	/**
	 * @var array
	 */
	protected $metrics = [];

	/**
	 * @var array
	 */
	protected $availableMetrics = [
		'players_total' => 'Players total',
		'players_active' => 'Players active',
		'players_online' => 'Players online',
	];

	/**
	 * @var EntityManager
	 */
	protected $em;

	/**
	 * @var StatDailyManager
	 */
	protected $statDailyManager;



	public function __construct(EntityManager $em, StatDailyManager $statDailyManager)
	{
		$this->em = $em;

		$this->from = DateTime::from(strtotime('now -1 month'));
		$this->to = new DateTime();
		$this->metrics = array_keys($this->availableMetrics);
		$this->statDailyManager = $statDailyManager;
	}



	public function render()
	{
		$template = parent::createTemplate();
		$template->stats = $this->statDailyManager->getStats($this->from, $this->to);
		$template->metrics = $this->metrics;
		$this['statsFilterForm']['metrics']->setValue($this->metrics);

		$template->setFile(__DIR__ . '/stats.latte');
		$template->render();
	}



	/**
	 * @return Form
	 */
	protected function createComponentStatsFilterForm()
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
		return $form->setBootstrapRenderer();
	}
}



interface IStatsControlFactory
{

	/** @return StatsControl */
	function create();
}
