<?php

namespace Teddy\Console;

use Kdyby\Doctrine\EntityManager;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Teddy\Entities\Stats\StatDetailedManager;



/**
 * Example usage: php www/index.php teddy:generateDetailedStats
 */
class GenerateDetailedStatsCommand extends \Game\Console\CronCommand
{

	/**
	 * @var EntityManager
	 * @inject
	 */
	public $em;

	/**
	 * @var StatDetailedManager
	 * @inject
	 */
	public $statDetailedManager;



	protected function configure()
	{
		$this->setName('teddy:generateDetailedStats')
			->setDescription('Generates detailed stats');
	}



	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$this->statDetailedManager->create();
		return 0;
	}

}
