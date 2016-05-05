<?php

require __DIR__ . '/../vendor/autoload.php';
include __DIR__ . '/shortcuts.php';

$configurator = new Teddy\Configurator;
$configurator->createRobotLoader()
	->addDirectory(__DIR__)
	->addDirectory(__DIR__ . '/../game')
	->addDirectory(__DIR__ . '/../game/presenters')
	->register();

$container = $configurator->createContainer();

return $container;
