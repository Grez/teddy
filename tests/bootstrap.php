<?php

require __DIR__ . '/../vendor/autoload.php';

if (!class_exists('Tester\Assert')) {
	echo "Install Nette Tester using `composer update --dev`\n";
	exit(1);
}

Tester\Environment::setup();

$parameters = ['wwwDir' => __DIR__ . '/../www'];
$configurator = new Teddy\Configurator($parameters);
$configurator->createRobotLoader()
	->addDirectory(__DIR__ . '/../app')
	->register();

$container = $configurator->createContainer();
