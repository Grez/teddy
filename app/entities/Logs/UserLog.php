<?php

namespace Teddy\Entities\Logs;

use Nette;
use Teddy\Entities;
use Doctrine\ORM\Mapping as ORM;
use Teddy\TeddyException;



/**
 * @ORM\MappedSuperclass()
 */
abstract class UserLog extends \Kdyby\Doctrine\Entities\BaseEntity
{

	/**
	 * @ORM\ManyToOne(targetEntity="\Game\Entities\User\User")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 * @var \Game\Entities\User\User
	 */
	protected $user;

	/**
	 * 0 - 1000 reserved for Teddy
	 *
	 * @ORM\Column(type="integer")
	 * @var int
	 */
	protected $action = 0;

	/**
	 * @ORM\Column(type="boolean")
	 * @var int
	 */
	protected $type = 0;

	/**
	 * @ORM\Column(type="array")
	 * @var array
	 */
	protected $data;

	/**
	 * @ORM\Column(type="datetime")
	 * @var \DateTime
	 * Generated in __construct()
	 */
	protected $date;

	/** Action types */
	const ADMIN = 1;
	const USER = 2;
	const GAME = 3;

	/** Admin actions */
	const ADMIN_DAEMON = 1;
	const ADMIN_EDIT_USER = 2;
	const ADMIN_DELETE_USER = 3;
	const ADMIN_BAN_USER = 4;
	const ADMIN_CREATE_ADMIN = 5;
	const ADMIN_EDIT_ADMIN = 6;
	const ADMIN_DELETE_ADMIN = 7;
	const ADMIN_BAN_IP = 8;
	const ADMIN_UNBAN_IP = 9;
	const ADMIN_REACTIVATE_USER = 10;
	const ADMIN_CHANGE_PASSWORD = 11;
	const ADMIN_CHANGE_USER_NICK = 12;

	/** User actions */
	const USER_CHANGE_PASSWORD = 1;
	const USER_DELETE_USER = 2;

	/** Game actions */



	public function __construct()
	{
		$this->date = new \DateTime();
	}



	/**
	 * @TODO: refactor (\Kdyby\Translations?)
	 * @return string
	 */
	public function getMessage()
	{
		$data = (is_array($this->getData())) ? $this->getData() : [$this->getData()];
		return vsprintf($this->getTemplate(), $data);
	}



	/**
	 * @return string
	 * @throws TeddyException
	 */
	protected function getTemplate()
	{
		$templates = [
			self::ADMIN => [
				self::ADMIN_DAEMON => 'Daemon %s',
				self::ADMIN_EDIT_USER => 'Edited user %s',
				self::ADMIN_DELETE_USER => 'Deleted user %s',
				self::ADMIN_BAN_USER => 'Banned user %s',
				self::ADMIN_CREATE_ADMIN => 'Created admin %s',
				self::ADMIN_EDIT_ADMIN => 'Edited admin %s',
				self::ADMIN_DELETE_ADMIN => 'Deleted admin %s',
				self::ADMIN_BAN_IP => 'Banned ip %s, for %s days, reason: %s',
				self::ADMIN_UNBAN_IP => 'Unbanned ip %s (reason of ban: %s)',
				self::ADMIN_REACTIVATE_USER => 'Reactivated user %s',
				self::ADMIN_CHANGE_PASSWORD => 'Changed password for user %s',
				self::ADMIN_CHANGE_USER_NICK => 'Changed user nick from %s to %s',
			],
			self::USER => [
				self::USER_CHANGE_PASSWORD => 'Changed password',
				self::USER_DELETE_USER => 'Deleted profile',
			],
			self::GAME => [],
		];

		if (isset($templates[$this->type][$this->action])) {
			return $templates[$this->type][$this->action];
		}

		throw new TeddyException('Unknown action ' . $this->type . ':' . $this->action);
	}

}
