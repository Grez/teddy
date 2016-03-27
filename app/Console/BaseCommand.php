<?php

namespace Teddy\Console;

use Kdyby;
use Nette;
use Symfony\Component\Console;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;



abstract class BaseCommand extends Console\Command\Command
{

	/**
	 * @var \Kdyby\Doctrine\EntityManager
	 * @inject
	 */
	public $entityManager;

	/**
	 * @var \Kdyby\Clock\IDateTimeProvider
	 * @inject
	 */
	public $dateTimeProvider;

	/**
	 * @var InputInterface
	 */
	protected $input;

	/**
	 * @var OutputInterface
	 */
	protected $output;

	/**
	 * @var float
	 */
	private $commandStartTime;



	protected function initialize(InputInterface $input, OutputInterface $output)
	{
		parent::initialize($input, $output);

		$this->input = $input;
		$this->output = $output;

		$this->commandStartTime = microtime(TRUE);
	}



	public function run(InputInterface $input, OutputInterface $output)
	{
		$code = parent::run($input, $output);

		$this->output->writeln('');
		$arguments = implode(' ', array_slice($_SERVER['argv'], 2));

		$memoryUsage = (int) (memory_get_usage(TRUE) / 1024 / 1024);
		$memoryLimit = ini_get('memory_limit');
		$elapsedTime = microtime(TRUE) - $this->commandStartTime;
		$msg = sprintf('Command %s %s finished in %.3f, memory: %s/%s', $this->getName(), $arguments, $elapsedTime, $memoryUsage, $memoryLimit);
		$this->output->writeln('<info>' . $msg . '</info>');

		return $code;
	}

}
