<?php

namespace Teddy\Entities\Map;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Teddy\Console\BaseCommand;



class EmbiggenMapCommand extends BaseCommand
{

	/**
	 * @var MapService
	 * @inject
	 */
	public $mapService;



	protected function configure()
	{
		$this->setName('teddy:embiggenMap')
			->addOption('size', 's', InputOption::VALUE_REQUIRED, 'Borders to add')
			->addOption('map', 'm', InputOption::VALUE_REQUIRED, 'Map id')
			->setDescription('Embiggens map');
	}



	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$mapId = intVal($input->getOption('map'));
		$sizeToAdd = intVal($input->getOption('size'));

		$map = $this->mapService->getMap($mapId);
		if (!$map) {
			$this->output->writeln('Map not found');
			return 0;
		}

		if ($sizeToAdd <= 0) {
			$this->output->writeln('You need to add at least one border');
			return 0;
		}

		$progress = new ProgressBar($output, $sizeToAdd);
		$progress->setFormat(ProgressBar::getFormatDefinition('debug'));
		$progress->start();

		$this->mapService->onEmbiggen[] = function (MapService $mapService, Map $map) use ($progress) {
			$progress->advance();
		};

		$this->mapService->embiggenMapBy($map, $sizeToAdd);
		$progress->finish();
		return 0;
	}

}
