<?php

namespace Teddy\Model;

use Nette;
use Kdyby\Doctrine\EntityManager;


class Bans extends Manager
{

    public function __construct(EntityManager $em)
    {
        parent::__construct($em);
        $this->repository = $this->em->getRepository(\Teddy\Model\Ban::class);
    }

    /**
     * @param string $ip
     * @param string $reason
     * @param int $days
     * @param string $type
     * @return NULL
     */
    public function ban($ip, $reason = '', $days = 0, $type = Ban::GAME)
    {
        // Max && default ban = 10 years
        if($days == 0 || $days >= 3650) {
            $days = 3650;
        }

        $ban = new Ban();
        $ban->setIp($ip);
        $ban->setReason($reason);
        $ban->setType($type);

        if ($days) {
            $until = new \Nette\Utils\DateTime();
            $until->setTimestamp(time() + $days * 86400);
            $ban->setUntil($until);
        }

        $this->em->persist($ban);
        $this->em->flush();
    }

    /**
     * @param string $ip
     * @return array|bool
     */
    public function checkIp($ip)
    {
        $long = ip2long($ip);
        $criteria = array(
            'start =' => $long,
            'until >=' => new \DateTime(),
        );
        $data = $this->repository->findBy($criteria);

        if (count($data)) {
            return $data;
        }

        $criteria = array(
            'start <=' => $long,
            'end >=' => $long,
            'until >=' => new \DateTime(),
        );
        $data = $this->repository->findBy($criteria);

        if (count($data)) {
            return $data;
        }

        return false;
    }

    /**
     * Checks whether IP is totally banned from website (403 error code)
     * @param $ip
     * @return Ban|bool
     */
    public function hasTotalBan($ip)
    {
        $bans = $this->checkIp($ip);
        if (is_array($bans)) {
            foreach ($bans as $ban) {
                if ($ban->getType() == Ban::TOTAL) {
                    return $ban;
                }
            }
        }
        return false;
    }

    /**
     * Checks whether IP can login
     * @param string $ip
     * @return Ban|bool
     */
    public function hasLoginBan($ip)
    {
        $bans = $this->checkIp($ip);
        if (is_array($bans)) {
            foreach ($bans as $ban) {
                if (in_array($ban->getType(), array(Ban::TOTAL, Ban::GAME))) {
                    return $ban;
                }
            }
        }
        return false;
    }

    /**
     * Checks whether IP can register
     * @param string $ip
     * @return Ban|bool
     */
    public function hasRegistrationBan($ip)
    {
        $bans = $this->checkIp($ip);
        if (is_array($bans)) {
            foreach ($bans as $ban) {
                if (in_array($ban->getType(), array(Ban::TOTAL, Ban::REGISTRATION, Ban::GAME))) {
                    return $ban;
                }
            }
        }
        return false;
    }

    public function getBans()
    {
        return $this->findBy(array());
    }

}