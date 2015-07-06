<?php

namespace Teddy\Model;

use Nette;
use Kdyby\Doctrine\EntityManager;


class UserLogs extends Manager
{

    public function __construct(EntityManager $em)
    {
        parent::__construct($em);
        $this->dao = $this->em->getRepository(\Teddy\Model\UserLog::class);
    }

    public function log(User $user, $type, $action, $data = null)
    {
        $userLog = new UserLog;
        $userLog->setUser($user);
        $userLog->setType($type);
        $userLog->setAction($action);
        $userLog->setData($data);
        $this->save($userLog);
    }

    public function getLogs($type)
    {
        $criteria = array(
            'type' => $type,
        );
        return $this->findBy($criteria);
    }

}
