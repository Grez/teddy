<?php

namespace Teddy\Entities\Map;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Teddy\Console\BaseCommand;



class GenerateMapCommand extends BaseCommand
{

	/**
	 * @var MapService
	 * @inject
	 */
	public $mapService;



	protected function configure()
	{
		$this->setName('teddy:generateMap')
			->addOption('radius', 'r', InputOption::VALUE_REQUIRED, 'Radius (do not use too big! Use enlarge command)')
			->setDescription('Generates map');
	}



	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$radius = intVal($input->getOption('radius'));
		$map = $this->mapService->createMap($radius);
		$this->output->writeln('Map id: ' . $map->getId());

		return 0;
	}

}
