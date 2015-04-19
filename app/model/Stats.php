<?php

namespace Teddy\Model;

use Kdyby\Doctrine\EntityDao;
use Kdyby\Doctrine\EntityManager;
use Nette;
use Teddy\Model\Users;

class Stats extends Manager
{

    /** @var Users */
    protected $users;


    public function __construct(EntityDao $dao, EntityManager $em, Users $users)
    {
        parent::__construct($dao, $em);
        $this->users = $users;
    }

    /**
     * Saves statistics about players + server
     * Used by cron_minute
     * @return NULL
     */
    public function createMinuteStats()
    {
        $stat = new Stat();
        $stat->setDate(new \DateTime());
        $stat->setPlayers($this->users->getTotal());
        $stat->setPlayersActive($this->users->getTotal(true));
        $stat->setPlayersOnline($this->users->getOnline());

        if(function_exists('sys_getloadavg')) {
            $load = sys_getloadavg();
            $stat->setLoad1($load[0]);
            $stat->setLoad5($load[1]);
            $stat->setLoad15($load[2]);
        };

        $this->em->persist($stat);
        $this->em->flush();
    }
}