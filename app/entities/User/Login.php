<?php

namespace Teddy\Entities\User;

use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Kdyby\Doctrine\Entities\BaseEntity;
use Kdyby\Doctrine\Entities\MagicAccessors;
use Nette;
use Teddy\Entities;
use Doctrine\ORM\Mapping as ORM;



/**
 * @ORM\MappedSuperclass()
 * @ORM\Table(indexes={
 *   @ORM\Index(columns={"ip"}),
 *   @ORM\Index(columns={"cookie"}),
 *   @ORM\Index(columns={"fingerprint"})
 * })
 */
abstract class Login extends BaseEntity
{

	/**
	 * @ORM\ManyToOne(targetEntity="\Game\Entities\User\User")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 */
	protected $user;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $login = '';

	/**
	 * @ORM\ManyToOne(targetEntity="\Game\Entities\User\UserAgent", cascade="persist")
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

	const ERROR_WRONG_EMAIL = 1;
	const ERROR_WRONG_PASSWORD = 2;
	const ERROR_USER_BANNED = 3;
	const ERROR_IP_BANNED = 4;



	public function __construct(Nette\Http\Request $request, UserAgent $userAgent, $login = '', User $user = NULL, $error = 0)
	{
		$this->date = new \DateTime();
		$this->userAgent = $userAgent;
		$this->ip = $request->getRemoteAddress();
		$this->cookie = $request->getCookie('login') ?: NULL;
		$this->fingerprint = $request->getCookie('fingerprint') ?: NULL;
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
