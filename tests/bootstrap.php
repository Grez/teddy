<?php

require __DIR__ . '/../vendor/autoload.php';

Kdyby\TesterExtras\Bootstrap::setup(__DIR__);


if (!class_exists('Tester\Assert')) {
	echo "Install Nette Tester using `composer update --dev`\n";
	exit(1);
}

Tester\Environment::setup();
