<?php

namespace Teddy\Entities\Bans;

use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Nette;
use Teddy\Entities;
use Doctrine\ORM\Mapping as ORM;



/**
 * @ORM\Entity()
 * @ORM\Table(indexes={
 *   @ORM\Index(columns={"range_start"}),
 *   @ORM\Index(columns={"range_end"}),
 *   @ORM\Index(columns={"ends_at"})
 * })
 * @TODO: IPv6, unsigned is only for some engines
 */
class Ban extends \Kdyby\Doctrine\Entities\BaseEntity
{

	use Identifier;

	/**
	 * @ORM\Column(type="integer", options={"unsigned"=TRUE})
	 * @var int
	 */
	protected $rangeStart;

	/**
	 * @ORM\Column(type="integer", options={"unsigned"=TRUE})
	 * @var int
	 */
	protected $rangeEnd;

	/**
	 * Generated in __construct()
	 * @ORM\Column(type="datetime")
	 * @var \DateTime
	 */
	protected $createdAt;

	/**
	 * @ORM\Column(type="datetime")
	 * @var \DateTime
	 */
	protected $endsAt;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	protected $reason;

	/**
	 * @ORM\Column(type="integer")
	 * @var int
	 */
	protected $type;

	/** Registration from this IP is banned */
	const REGISTRATION = 1;

	/** Both registration and login */
	const GAME = 2;

	/** 403 error code, user is totally banned from site (DoS etc.) */
	const TOTAL = 3;


	/**
	 * @param string $ip
	 * @param string $reason
	 * @param \DateTime $endsAt
	 * @param int $type
	 */
	public function __construct($ip, $reason = '', \DateTime $endsAt = NULL, $type = self::GAME)
	{
		$maxBan = new \DateTime('2100-01-01');
		$this->endsAt =  $endsAt !== NULL && $endsAt < $maxBan ? $endsAt : $maxBan;

		$this->createdAt = new \DateTime();
		$this->type = $type;
		$this->reason = $reason;
		$this->setIp($ip);
	}



	/**
	 * Accepts asterisk for range 0-255
	 *
	 * @param string $ip
	 */
	protected function setIp($ip)
	{
		if (strpos($ip, '*') !== FALSE) {
			$start = str_replace('*', '0', $ip);
			$end = str_replace('*', '255', $ip);
			$this->rangeStart = ip2long($start);
			$this->rangeEnd = ip2long($end);

		} else {
			$this->rangeStart = ip2long($ip);
			$this->rangeEnd = ip2long($ip);
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
		if ($this->rangeEnd > 0) {
			$range = $this->rangeEnd - $this->rangeStart;
			$ip = long2ip($this->rangeStart);
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
		return long2ip($this->rangeStart);
	}



	/**
	 * @return \DateTime
	 */
	public function getCreatedAt()
	{
		return $this->createdAt;
	}



	/**
	 * @return \DateTime
	 */
	public function getEndsAt()
	{
		return $this->endsAt;
	}



	/**
	 * @return string
	 */
	public function getReason()
	{
		return $this->reason;
	}

}
