<?php

namespace Teddy\Model;

use Kdyby\Doctrine\EntityDao;
use Kdyby\Doctrine\EntityManager;
use Nette;
use Teddy\Model\Users;


class StatDetailedManager extends Manager
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
    public function create()
    {
        $stat = new Stat();
        $stat->setDate(new \DateTime());
        $stat->setTime(new \DateTime());
        $stat->setPlayersTotal($this->users->getTotal());
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

    /**
     * @param \DateTime|null $from
     * @param \DateTime|null $to
     * @return array
     */
    public function getStats($from = null, $to = null, $types = [])
    {
        $criteria = [];

        if (!$from instanceof \DateTime) {
            $from = new \DateTime('now -1 month');
        }

        if (!$to instanceof \DateTime) {
            $to = new \DateTime('now');
        }

        $criteria['date >='] = $from;
        $criteria['date <='] = $to;

        return $this->dao->findBy($criteria, ['date' => 'DESC'], 30);
    }

}