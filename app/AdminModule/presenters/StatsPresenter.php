<?php

namespace Teddy\AdminModule\Presenters;

use Nette\Utils\DateTime;
use Teddy;
use Teddy\Forms\Form;


class StatsPresenter extends BasePresenter
{

    /** @var Teddy\Model\StatDailyManager @inject */
    public $statsDailyManager;

    /** @var Teddy\Model\StatDetailedManager @inject */
    public $statsDetailedManager;

    /** @var \DateTime|null */
    protected $from = null;

    /** @var \DateTime|null */
    protected $to = null;

    public function startup()
    {
        parent::startup();
        $this->from = DateTime::from(strtotime('now -1 month'));
        $this->to = new DateTime();
    }


    public function renderDefault()
    {
//        set_time_limit(1000);
//        $time = strtotime('2014-01-01');
//        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);
//        for($i = 0; $i <= 365; $i++) {
//            $this->statsDailyManager->create(\Nette\Utils\DateTime::from($time + 86400 * $i));
//            $this->em->clear();
//        }

//        $query = (new Teddy\Model\StatQuery())
//            ->filtered($this['filter']->getFilter());
//        $this->template->stats = $this->stats->fetch($query);
//        $this->template->stats = $this->stats->getStats($this->from, $this->to);
//        barDump('render');

//        $count = 0;
//        $objectsPerChunk = 1000;
//set_time_limit(1000);
//
//        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);
//
//        $time = strtotime('2015-01-01');
//        for ($i = 0; $i < 1440 * 3; $i++) {
//            $stat = new Teddy\Model\Stat();
//            $stat->setDate(DateTime::from($time + 60 * $i));
//            $stat->setTime(DateTime::from($time + 60 * $i));
//            $stat->setPlayers(mt_rand(3000, 5000));
//            $stat->setPlayersOnline(mt_rand(0, 300));
//            $stat->setPlayersActive(mt_rand(1000, 3000));
//            $this->em->persist($stat);
//
//            $count++;
//            if ($count%$objectsPerChunk == 0) {
//                $this->em->flush();
//                $this->em->clear();
//            }
//
//            unset($stat);
//        }
//        $this->em->flush();
//        exit('yup');
        $this->template->stats = $this->statsDailyManager->getStats($this->from, $this->to);
        barDump('ren');
    }

    public function actionDefault()
    {
        barDump('act');
    }

//    protected function createComponentFilter()
//    {
//        return $this->filterFormFactory->create();
//    }

    /**
     * @return Form
     */
    protected function createComponentStatsFilterForm()
    {
        $form = new Form();
        $form->addDate('from', 'From', 'd.m.Y')
            ->setDefaultValue($this->from)
            ->setRequired('Please fill date')
            ->addRule([$form['from'], 'validateDate'], 'Date is invalid');
        $form->addDate('to', 'To', 'd.m.Y')
            ->setDefaultValue($this->to)
            ->setRequired('Please fill date')
            ->addRule([$form['to'], 'validateDate'], 'Date is invalid');
        $form->addCheckboxList('metrics', 'Metrics', array(
            'players_total' => 'Players total',
            'players_online' => 'Players online',
            'players_active' => 'Players active',
        ));
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
    }

}
