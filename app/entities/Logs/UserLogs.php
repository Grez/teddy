<?php

namespace Teddy\Entities\Logs;

use Nette;
use Teddy\Entities;
use Kdyby\Doctrine\EntityManager;
use Teddy\Entities\User\User;



class UserLogs extends Entities\Manager
{

	public function __construct(EntityManager $em)
	{
		parent::__construct($em);
		$this->repository = $this->em->getRepository(\Game\Entities\Logs\UserLog::class);
	}



	public function log(User $user, $type, $action, $data = NULL)
	{
		$userLog = new \Game\Entities\Logs\UserLog;
		$userLog->setUser($user);
		$userLog->setType($type);
		$userLog->setAction($action);
		$userLog->setData($data);
		$this->save($userLog);
	}



	public function getLogs($type)
	{
		$criteria = [
			'type' => $type,
		];
		return $this->findBy($criteria);
	}

}
