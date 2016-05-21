<?php

namespace Teddy\Entities\User;

use Kdyby\Doctrine\Entities\BaseEntity;
use Nette;
use Teddy\Entities;
use Doctrine\ORM\Mapping as ORM;



/**
 * @ORM\MappedSuperclass()
 */
abstract class Login extends BaseEntity
{

	/**
	 * @ORM\ManyToOne(targetEntity="\Game\Entities\User\User")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 * @var \Game\Entities\User\User
	 */
	protected $user;

	/**
	 * @ORM\Column(type="string", nullable=FALSE)
	 * @var string
	 */
	protected $login;

	/**
	 * @ORM\ManyToOne(targetEntity="\Game\Entities\User\UserAgent", cascade="persist")
	 * @ORM\JoinColumn(name="user_agent_id", nullable=false, referencedColumnName="id")
	 * @var \Game\Entities\User\UserAgent
	 */
	protected $userAgent;

	/**
	 * @ORM\Column(type="string", nullable=TRUE)
	 * @var string
	 */
	protected $ip;

	/**
	 * @ORM\Column(type="integer", nullable=TRUE)
	 * @var int
	 */
	protected $cookie;

	/**
	 * @ORM\Column(type="string", nullable=TRUE)
	 * @var string
	 */
	protected $fingerprint;

	/**
	 * @ORM\Column(type="datetime")
	 * @var \DateTime
	 * Generated in __construct()
	 */
	protected $date;

	/**
	 * @ORM\Column(type="integer")
	 * @var int
	 */
	protected $error = 0;

	const ERROR_WRONG_NICK = 1;
	const ERROR_WRONG_PASSWORD = 2;
	const ERROR_USER_BANNED = 3;
	const ERROR_IP_BANNED = 4;



	public function __construct($ip, $cookie, $fingerprint, UserAgent $userAgent, $login = '', User $user = NULL, $error = 0)
	{
		$this->date = new \DateTime();
		$this->userAgent = $userAgent;
		$this->ip = $ip;
		$this->cookie = $cookie;
		$this->fingerprint = $fingerprint;
		$this->login = $login;
		$this->user = $user;
		$this->error = $error;
	}



	/**
	 * @return mixed
	 */
	public function getError()
	{
		return $this->error;
	}



	/**
	 * @return mixed
	 */
	public function getUser()
	{
		return $this->user;
	}



	/**
	 * @return mixed
	 */
	public function getLogin()
	{
		return $this->login;
	}

}
