<?php

namespace Teddy\Entities\Logs;

use Nette;
use Teddy\Entities;
use Kdyby\Doctrine\EntityManager;



class SystemLogs extends Entities\Manager
{

	public function __construct(EntityManager $em)
	{
		parent::__construct($em);
		$this->repository = $this->em->getRepository(\Game\Entities\Logs\SystemLog::class);
	}



	/**
	 * @param string $script - script name, must be mapped in SystemLog::$scripts
	 * @param string $action - action name, must be mapped in SystemLog::$actions
	 * @param string $description
	 * @return NULL
	 * @throws \InvalidArgumentException
	 */
	public function log($script, $action, $description)
	{
		$log = new \Game\Entities\Logs\SystemLog();
		$log->setScript(SystemLog::getScriptId($script));
		$log->setAction(SystemLog::getActionId($action));
		$log->setDescription($description);

		$this->em->persist($log);
		$this->em->flush();
	}

}
