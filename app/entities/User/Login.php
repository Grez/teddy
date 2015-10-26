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
	 * @ORM\ManyToOne(targetEntity="UserAgent", nullable="false", cascade="persist")
	 * @ORM\JoinColumn(name="user_agent_id", referencedColumnName="id")
	 */
	protected $userAgent;

	/**
	 * @ORM\Column(type="string")
	 * Set in __construct()
	 */
	protected $ip = '';

	/**
	 * @ORM\Column(type="integer")
	 * Set in __construct()
	 */
	protected $cookie = 0;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $fingerprint = '';

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



	public function __construct(UserAgent $userAgent)
	{
		$this->date = new \DateTime();
		$this->userAgent = $userAgent;
		$this->ip = $_SERVER['REMOTE_ADDR'];
		$this->setCookie();
	}



	/**
	 * Sets tracking cookie (uses current if available)
	 */
	public function setCookie()
	{
		$this->cookie = isset($_COOKIE['login']) ? $_COOKIE['login'] : mt_rand(1000000, 9999999);
		setcookie('login', $this->cookie, time() + 86400 * 365);
	}

}
