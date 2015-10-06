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
		$this->repository = $this->em->getRepository(UserLog::class);
	}



	public function log(User $user, $type, $action, $data = NULL)
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
