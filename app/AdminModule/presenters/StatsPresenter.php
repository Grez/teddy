<?php

namespace Teddy\AdminModule\Presenters;

use Nette\Utils\DateTime;
use Teddy;
use Teddy\Forms\Form;


class StatsPresenter extends BasePresenter
{

    /** @var Teddy\Entities\Stats\StatDailyManager @inject */
    public $statsDailyManager;

    /** @var \DateTime|null */
    protected $from = null;

    /** @var \DateTime|null */
    protected $to = null;

    /** @var array */
    protected $metrics = [];

    /** @var array */
    protected $availableMetrics = [
        'players_total' => 'Players total',
        'players_active' => 'Players active',
        'players_online' => 'Players online',
    ];


    public function startup()
    {
        parent::startup();
        $this->from = DateTime::from(strtotime('now -1 month'));
        $this->to = new DateTime();
        $this->metrics = array_keys($this->availableMetrics);
    }

    public function renderDefault()
    {
        $this->template->stats = $this->statsDailyManager->getStats($this->from, $this->to);
        $this->template->metrics = $this->metrics;
        $this['statsFilterForm']['metrics']->setValue($this->metrics);
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
        $form->onSuccess[] = $this->statsFormSuccess;
        return $form;
    }

    /**
     * @param Form $form
     * @param \Nette\Utils\ArrayHash $values
     */
    public function statsFormSuccess($form, $values)
    {
        if ($values['from'] instanceof \DateTimeImmutable) {
            $this->from = \Nette\Utils\DateTime::from($values['from']->getTimestamp());
        }

        if ($values['to'] instanceof \DateTimeImmutable) {
            $this->to = \Nette\Utils\DateTime::from($values['to']->getTimestamp());
        }

        $this->metrics = ($values['metrics']) ?: array_keys($this->availableMetrics);
    }

}
