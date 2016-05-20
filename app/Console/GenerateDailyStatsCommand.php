<?php

namespace Teddy\Console;

use Kdyby\Doctrine\EntityManager;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Teddy\Entities\Stats\StatDailyManager;



/**
 * Example usage: php www/index.php teddy:generateDailyStats -d 2016-05-17
 */
class GenerateDailyStatsCommand extends \Game\Console\CronCommand
{

	/**
	 * @var EntityManager
	 * @inject
	 */
	public $em;

	/**
	 * @var StatDailyManager
	 * @inject
	 */
	public $statDailyManager;



	protected function configure()
	{
		$this->setName('teddy:generateDailyStats')
			->addOption('date', 'd', InputOption::VALUE_OPTIONAL, 'Date in "Y-m-d" format')
			->setDescription('Generates Daily stats');
	}



	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$inputDate = ($input->getOption('date'));
		$date = $inputDate ? new \DateTime($inputDate) : $this->dateTimeProvider->getDate()->modify('-1 day');

		$this->statDailyManager->create($date);
		return 0;
	}

}
