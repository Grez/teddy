<?php

namespace Teddy\Entities\User;

use Nette;
use Teddy\Entities;
use Doctrine\ORM\Mapping as ORM;



/**
 * @ORM\Entity()
 */
class Login extends \Kdyby\Doctrine\Entities\BaseEntity
{

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 */
	protected $id;

	/**
	 * @ORM\ManyToOne(targetEntity="User")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 */
	protected $user;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $login = '';

	/**
	 * @ORM\ManyToOne(targetEntity="UserAgent", cascade="persist")
	 * @ORM\JoinColumn(name="user_agent_id", nullable=false, referencedColumnName="id")
	 */
	protected $userAgent;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 * Set in __construct()
	 */
	protected $ip;

	/**
	 * @ORM\Column(type="integer", nullable=true)
	 * Set in __construct()
	 */
	protected $cookie;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 * Set in __construct()
	 */
	protected $fingerprint;

	/**
	 * @ORM\Column(type="datetime")
	 * Set in __construct()
	 */
	protected $date;

	/**
	 * @ORM\Column(type="integer")
	 */
	protected $error = 0;

	const ERROR_WRONG_LOGIN = 1;
	const ERROR_WRONG_PASSWORD = 2;
	const ERROR_USER_BANNED = 3;
	const ERROR_IP_BANNED = 4;



	public function __construct(Nette\Http\Request $request, UserAgent $userAgent, $login = '', User $user = NULL, $error = 0)
	{
		$this->date = new \DateTime();
		$this->userAgent = $userAgent;
		$this->ip = $request->getRemoteAddress();
		$this->cookie = $request->getCookie('login') ?: mt_rand(1000000, 9999999);
		$this->fingerprint = $request->getCookie('fingerprint') ?: NULL;
		$this->user = $user;
		$this->login = $login;
	}



	/**
	 * @param mixed $error
	 * @return Login
	 */
	public function setError(mixed $error)
	{
		$this->error = $error;
		return $this;
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
