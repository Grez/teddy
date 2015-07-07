<?php

namespace Teddy\Model;

use Nette;
use Kdyby\Doctrine\EntityManager;


class SystemLogs extends Manager
{

    public function __construct(EntityManager $em)
    {
        parent::__construct($em);
        $this->repository = $this->em->getRepository(\Teddy\Model\SystemLog::class);
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
        $log = new SystemLog();
        $log->setScript(SystemLog::getScriptId($script));
        $log->setAction(SystemLog::getActionId($action));
        $log->setDescription($description);

        $this->em->persist($log);
        $this->em->flush();
    }

}