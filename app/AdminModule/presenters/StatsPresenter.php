<?php

namespace Teddy\AdminModule\Presenters;

use Teddy\Forms\Form;


class StatsPresenter extends BasePresenter
{

    /**
     * @return Form
     */
    protected function createComponentStatsForm()
    {
        $form = new Form();
        $form->addDate('from', 'From', 'd.m.Y')
            ->addCondition(Form::FILLED)
                ->addRule([$form['from'], 'validateDate'], 'Date is invalid');
        $form->addDate('to', 'Till', 'd.m.Y');
        $form->addCheckboxList('metrics', 'Metrics', array(
            'system' => 'System',
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

    }

}