<?php

namespace Teddy\Tests;



use Kdyby\Doctrine\Connection;
use Kdyby\Doctrine\EntityManager;
use Kdyby\TesterExtras\DbConnectionMock;
use Nette\DI\Container;
use Teddy;
use Tester\Assert;


abstract class TestCase extends \Tester\TestCase
{

	/**
	 * @var Container
	 */
	private $container;

	/**
	 * @var Presenter
	 */
	protected $presenter;

	/**
	 * @var DomQuery
	 */
	protected $dom;

	/**
	 * @var UserContext
	 */
	protected $userContext;

	/**
	 * @var array
	 */
	protected $configOverride = [];

	/**
	 * @var string
	 */
	private $databaseName;



	/**
	 * \Game is kinda wonky so it won't get autoloaded w/ this :/
	 */
	public function setUp()
	{
		parent::setUp();
		$this->getContainer();
	}



	/**
	 * @return Container
	 */
	protected function getContainer()
	{
		if ($this->container === NULL) {
			$this->container = $this->createContainer();
		}

		return $this->container;
	}


	/**
	 * @return Container
	 */
	protected function createContainer()
	{
		$rootDir = __DIR__ . '/..';

		$params = [
			'wwwDir' => $rootDir . '/www',
			'appDir' => $rootDir . '/app',
			'tempDir' => __DIR__ . '/tmp',
			'testMode' => TRUE
		];
		$configurator = (new Teddy\Configurator($params))
			->addConfig(__DIR__ . '/config.neon')
			->setDebugMode(TRUE);
		$configurator->createRobotLoader()
			->addDirectory(__DIR__ . '/../app')
			->addDirectory(__DIR__ . '/../game')
			->register();
		$container = $configurator->createContainer();

		/** @var DbConnectionMock $db */
		$db = $container->getByType(Connection::class); // we want Connection service, but we slip DbConnectionMock
		$db->onConnect[] = function (Connection $db) use ($container) {
			if ($this->databaseName !== NULL) {
				return;
			}

			try {
				$this->doSetupDatabase($db);

			} catch (\Exception $e) {
				\Tracy\Debugger::log($e, \Tracy\Debugger::ERROR);
				Assert::fail($e->getMessage());
			}
		};

		return $container;
	}


	/**
	 * Lazy creating of database
	 *
	 * @param Connection $db
	 */
	private function doSetupDatabase(Connection $db)
	{
		$this->databaseName = 'teddy_tests_' . getmypid();

		$db->exec('DROP DATABASE IF EXISTS `' . $this->databaseName . '`');
		$db->exec('CREATE DATABASE ' . $this->databaseName . ' COLLATE "utf8_czech_ci"');

		$db->exec('USE `' . $this->databaseName . '`');
		$db->transactional(function (Connection $db) {
			$db->exec('SET foreign_key_checks = 0;');
			$db->exec('SET @disable_triggers = 1;');

			\Kdyby\Doctrine\Helpers::loadFromFile($db, __DIR__ . '/schema.sql');
		});

		$db->exec('SET foreign_key_checks = 1;');
		$db->exec('SET @disable_triggers = NULL;');

		register_shutdown_function(function () use ($db) {
			try {
				$db->exec('DROP DATABASE IF EXISTS `' . $this->databaseName . '`');

			} catch (\Exception $e) {
				// stfu
			}
		});
	}



	/**
	 * @param string $type
	 * @return object
	 */
	public function getService($type)
	{
		$container = $this->getContainer();
		if ($object = $container->getByType($type, FALSE)) {
			return $object;
		}

		return $container->createInstance($type);
	}



	/**
	 * @return \Teddy\Security\User
	 */
	protected function getUser()
	{
		return $this->getService(UserContext::class);
	}



	/**
	 * @return \Kdyby\Doctrine\EntityManager
	 */
	protected function getEm()
	{
		return $this->getService(EntityManager::class);
	}

}
