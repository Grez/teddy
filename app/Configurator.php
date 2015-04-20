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
    public function __construct($parameters = null)
    {
        $this->parameters = $this->processParameters($parameters);

        if (!is_writable($this->parameters['tempDir'])) {
            throw new Nette\DirectoryNotFoundException('Temp directory "' . $this->parameters['tempDir'] . '" is not writable or doesn\'t exist.');
        }

        $this->setupDebugger();
        $this->addConfigFiles();
    }

    /**
     * @param array|null $parameters
     * @return array
     */
    protected function processParameters($parameters = null)
    {
        if (!isset($parameters['wwwDir'])) {
            $parameters['wwwDir'] = isset($_SERVER['SCRIPT_FILENAME']) ? dirname(realpath($_SERVER['SCRIPT_FILENAME'])) : NULL;
        }

        if (!isset($parameters['appDir'])) {
            $trace = debug_backtrace(PHP_VERSION_ID >= 50306 ? DEBUG_BACKTRACE_IGNORE_ARGS : false);
            $parameters['appDir'] = isset($trace[1]['file']) ? dirname($trace[1]['file']) : (isset($parameters['wwwDir']) ? realpath($parameters['wwwDir'] . '/../app') : null);
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
            $parameters['container'] = array(
                'class' => 'SystemContainer',
                'parent' => 'Nette\DI\Container',
            );
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
     * @return null
     */
    protected function addConfigFiles()
    {
        if (is_file($config = __DIR__ . '/app/config/config.neon')) {
            $this->addConfig($config);
        }

        $appDir = $this->parameters['appDir'];

        // Global config
        if (is_file($config = "$appDir/config/config.neon")) {
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
            $this->parameters['nette']['debugger']['email'] : null;

        $this->enableDebugger($logDir, $email);
    }

}
