<?php

namespace Teddy\AdminModule\Presenters;

use Nette;
use Teddy;
use Teddy\Forms\Form;
use Teddy\Model\UserLogsQuery;
use IPub\VisualPaginator\Components as VisualPaginator;


class MainPresenter extends BasePresenter
{

    /** @var user_id @persistent */
    public $userId;


    public function renderDefault($page)
    {
        $query = (new UserLogsQuery())
            ->sortByDate();

        if ($this->getRequest()->getParameter('userId') > 0) {
            $query->byUser($this->users->find($this->getRequest()->getParameter('userId')));
        }

        $result = $this->userLogs->fetch($query);
        $result->applyPaginator($this['visualPaginator']->getPaginator(), 20);
        $this->template->logs = $result;
    }

    /**
     * @TODO: filter actions, persistent parameters?
     * @return Form
     */
    protected function createComponentFilterForm()
    {
        $form = new Form();
        $form->setMethod('GET');
        $select = $form->addSelect('userId', 'Admin')
            ->setItems([0 => 'All'] + $this->users->findPairs(['admin' => true], "nick", [], "id"));

        if ($this->getRequest()->getParameter('userId') > 0) {
            $select->setDefaultValue($this->getRequest()->getParameter('userId'));
        }

        $form->addSubmit('send', 'Filter');
        return $form;
    }

    /**
     * @return VisualPaginator\Control
     */
    protected function createComponentVisualPaginator()
    {
        $control = new VisualPaginator\Control;
        $control->setTemplateFile('bootstrap.latte');
        return $control;
    }

}
