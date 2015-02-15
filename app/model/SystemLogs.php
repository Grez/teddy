<?php

namespace App\Model;

use Nette;

class SystemLogs extends Manager
{

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