<?php

namespace Teddy\Entities\Bans;

use Nette;
use Teddy\Entities;
use Doctrine\ORM\Mapping as ORM;



/**
 * @ORM\Entity()
 * @ORM\Table(indexes={
 *   @ORM\Index(columns={"start"}),
 *   @ORM\Index(columns={"end"}),
 *   @ORM\Index(columns={"until"})
 * })
 * @TODO: IPv6
 */
class Ban extends \Kdyby\Doctrine\Entities\BaseEntity
{

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 */
	protected $id;

	/**
	 * @ORM\Column(type="integer")
	 */
	protected $start;

	/**
	 * @ORM\Column(type="integer")
	 */
	protected $end = 0;

	/**
	 * @ORM\Column(type="datetime")
	 * Generated in __construct()
	 */
	protected $created;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	protected $until;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $reason;

	/**
	 * @ORM\Column(type="integer")
	 */
	protected $type;

	/** Registration from this IP is banned */
	const REGISTRATION = 1;

	/** Both registration and login */
	const GAME = 2;

	/** 403 error code, user is totally banned from site (DoS etc.) */
	const TOTAL = 3;



	public function __construct()
	{
		$this->created = new \DateTime();
	}



	/**
	 * Accepts asterisk for range 0-255
	 *
	 * @param string $ip
	 */
	public function setIp($ip)
	{
		if (strpos($ip, '*') !== FALSE) {
			$start = str_replace('*', '0', $ip);
			$end = str_replace('*', '255', $ip);
			$this->start = ip2long($start);
			$this->end = ip2long($end);
		} else {
			$this->start = ip2long($ip);
		}
	}



	/**
	 * Returns IP in readable format
	 * Uses asterisk for range 0-255
	 *
	 * @return string
	 */
	public function getIp()
	{
		if ($this->end > 0) {
			$range = $this->end - $this->start;
			$ip = long2ip($this->start);
			if ($range == 256 - 1) {
				return substr($ip, 0, -1) . '*';
			} else {
				if ($range == 256 * 256 - 1) {
					return substr($ip, 0, -3) . '*.*';
				} else {
					if ($range == 256 * 256 * 256 - 1) {
						return substr($ip, 0, -5) . '*.*.*';
					}
				}
			}
		}
		return long2ip($this->start);
	}

}
