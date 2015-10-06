<?php

namespace Teddy\AdminModule\Presenters;

use Nette;
use Teddy;



class MainPresenter extends BasePresenter
{

	/** @var Teddy\Forms\IUserLogsFilterFormFactory @inject */
	public $filterFormFactory;



	public function renderDefault()
	{
		$query = (new Teddy\Entities\Logs\UserLogsListQuery())
			->byType(Teddy\Entities\Logs\UserLog::ADMIN)
			->sortByDate();

		if ($this['filterForm']->userId > 0) {
			$query->byUser($this->users->find($this['filterForm']->userId));
		}

		$result = $this->userLogs->fetch($query);
		$result->applyPaginator($this['visualPaginator']->getPaginator(), 20);
		$this->template->logs = $result;
	}



	protected function createComponentFilterForm()
	{
		return $this->filterFormFactory->create();
	}

}
