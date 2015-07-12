<?php

namespace Teddy\Entities\Stats;

use Nette;
use Teddy\Entities;
use Kdyby\Doctrine\EntityManager;


class StatDailyManager extends Entities\Manager
{

    public function __construct(EntityManager $em)
    {
        parent::__construct($em);
        $this->repository = $this->em->getRepository(StatDaily::class);
    }

    /**
     * Saves statistics for day
     * @return NULL
     */
    public function create(\DateTime $date)
    {
        $query = $this->em->createQuery('
            SELECT MAX(u.playersTotal) AS playersTotal, MAX(u.playersActive) AS playersActive, MAX(u.playersOnline) AS playersOnline
            FROM Teddy\Model\StatDetailed u
            WHERE u.date = ?1
        ');
        $query->setParameter(1, $date);
        $stats = $query->getResult();

        $result = $this->findBy(['date' => $date]);
        $statDaily = (is_array($result) && count($result)) ? $result[0] : new StatDaily();
        $statDaily->setDate($date);
        $statDaily->setPlayersTotal($stats[0]['playersTotal']);
        $statDaily->setPlayersActive($stats[0]['playersActive']);
        $statDaily->setPlayersOnline($stats[0]['playersOnline']);

        $this->em->persist($statDaily);
        $this->em->flush();
    }

    /**
     * @param \DateTime|null $from
     * @param \DateTime|null $to
     * @return array
     */
    public function getStats(\DateTime $from = null, \DateTime $to = null)
    {
        $criteria = [];
        $criteria['date >='] = $from;
        $criteria['date <='] = $to;

        return $this->findBy($criteria, ['date' => 'DESC']);
    }

}
