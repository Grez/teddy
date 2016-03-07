<?php

namespace Teddy\Tests;



use Teddy;


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
			->setDebugMode(true);
		$configurator->createRobotLoader()
			->addDirectory(__DIR__ . '/../app')
			->register();

		$container = $configurator->createContainer();
		return $container;
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
	 * @return \Teddy\Security\UserContext
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
