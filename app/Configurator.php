<?php

namespace Teddy;

use Nette;



/**
 * @author Petr Morávek <petr@pada.cz>
 * @author Tom Bartoň <grez.cz@gmail.com>
 */
class Configurator extends Nette\Configurator
{

	/**
	 * @param array|null $parameters string value represents wwwDir parameter
	 * @throws Nette\DirectoryNotFoundException
	 */
	public function __construct($parameters = NULL)
	{
		$this->parameters = $this->processParameters($parameters);

		if (!is_writable($this->parameters['tempDir'])) {
			throw new Nette\DirectoryNotFoundException('Temp directory "' . $this->parameters['tempDir'] . '" is not writable or doesn\'t exist.');
		}

		if (!is_writable($this->parameters['wwwDir'] . '/webtemp')) {
			throw new Nette\DirectoryNotFoundException('Public temp directory "' . $this->parameters['wwwDir'] . '/temp is not writable or doesn\'t exist.');
		}

		$this->setupDebugger();
		$this->addConfigFiles();

		\Nella\Forms\DateTime\DateInput::register();
		\Nella\Forms\DateTime\DateTimeInput::register();
	}



	/**
	 * @param array|null $parameters
	 * @return array
	 */
	protected function processParameters($parameters = NULL)
	{
		if (!isset($parameters['wwwDir'])) {
			$parameters['wwwDir'] = isset($_SERVER['SCRIPT_FILENAME']) ? dirname(realpath($_SERVER['SCRIPT_FILENAME'])) : NULL;
		}

		if (!isset($parameters['appDir'])) {
			$trace = debug_backtrace(PHP_VERSION_ID >= 50306 ? DEBUG_BACKTRACE_IGNORE_ARGS : FALSE);
			$parameters['appDir'] = isset($trace[1]['file']) ? dirname($trace[1]['file']) : (isset($parameters['wwwDir']) ? realpath($parameters['wwwDir'] . '/../app') : NULL);
		}

		if (!isset($parameters['libsDir'])) {
			$parameters['libsDir'] = realpath($parameters['appDir'] . '/../vendor');
		}

		if (!isset($parameters['tempDir'])) {
			$parameters['tempDir'] = realpath($parameters['appDir'] . '/../temp');
		}

		if (!isset($parameters['logDir'])) {
			$parameters['logDir'] = realpath($parameters['appDir'] . '/../log');
		}

		if (!isset($parameters['container'])) {
			$parameters['container'] = [
				'class' => 'SystemContainer',
				'parent' => 'Nette\DI\Container',
			];
		}

		if (!isset($parameters['consoleMode'])) {
			$parameters['consoleMode'] = PHP_SAPI === 'cli';
		}

		if (!isset($parameters['debugMode'])) {
			$parameters['debugMode'] = static::detectDebugMode();
		}
		$parameters['productionMode'] = !$parameters['debugMode'];

		if (!isset($parameters['environment'])) {
			$parameters['environment'] = $parameters['debugMode'] ? 'development' : 'production';
		}

		return $parameters;
	}



	/**
	 * Adds config files
	 *
	 * @return null
	 */
	protected function addConfigFiles()
	{
		if (is_file($config = __DIR__ . '/config/config.neon')) {
			$this->addConfig($config);
		}

		$appDir = $this->parameters['appDir'];

		// Global config
		if ($appDir != __DIR__ && is_file($config = "$appDir/config/config.neon")) {
			$this->addConfig($config);
		}

		// Environment config
		if (isset($this->parameters['environment']) && is_file($config = "$appDir/config/{$this->parameters['environment']}.neon")) {
			$this->addConfig($config);
		}

		// Local config
		if (is_file($config = "$appDir/config/config.local.neon")) {
			$this->addConfig($config);
		}
	}



	/**
	 * Sets up the Debugger
	 *
	 * @throws Nette\DirectoryNotFoundException
	 */
	protected function setupDebugger()
	{
		if (!is_dir($logDir = $this->parameters['logDir'])) {
			@mkdir($logDir, 0777);
		}

		// check if log dir is writable
		if (!is_writable($logDir)) {
			throw new Nette\DirectoryNotFoundException("Log directory '" . $logDir . "' is not writable or doesn\'t exist.");
		}

		$email = isset($this->parameters['nette']['debugger']['email']) ?
			$this->parameters['nette']['debugger']['email'] : NULL;

		$this->enableDebugger($logDir, $email);
	}

}
