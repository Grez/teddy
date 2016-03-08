<?php

namespace Teddy\Entities\User;

use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Nette;
use Teddy\Entities;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Teddy\Entities\Coins\CoinSack;


/**
 * @ORM\Entity()
 */
class User extends \Kdyby\Doctrine\Entities\BaseEntity
{

	use Identifier;

	/**
	 * @ORM\Column(type="string", unique=true)
	 */
	protected $nick;

	/**
	 * @ORM\Column(type="string", unique=TRUE)
	 */
	protected $email;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $password;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $name = '';

	/**
	 * @ORM\Column(type="boolean")
	 */
	protected $emailVerified = 0;

	/**
	 * @ORM\Column(type="integer")
	 * Generated in __construct()
	 */
	protected $verificationCode = 0;

	/**
	 * @ORM\Column(type="smallint")
	 */
	protected $age = 0;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $location = '';

	/**
	 * @ORM\Column(type="smallint")
	 */
	protected $gender = 0;

	/**
	 * @ORM\Column(type="datetime")
	 * @var \DateTime
	 * Generated in __construct()
	 */
	protected $lastLoginAt;

	/**
	 * @ORM\Column(type="datetime")
	 * @var \DateTime
	 * Generated in __construct()
	 */
	protected $lastActivityAt;

	/**
	 * @ORM\Column(type="datetime")
	 * @var \DateTime
	 * Generated in __construct()
	 */
	protected $registeredAt;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $fbId = '';

	/**
	 * @ORM\Column(type="string")
	 */
	protected $avatar = '';

	/**
	 * @ORM\Column(type="integer")
	 */
	protected $donate = 0;

	/**
	 * @ORM\Column(type="boolean")
	 */
	protected $activated = FALSE;

	/**
	 * @ORM\Column(type="string")
	 * Generated in __construct()
	 */
	protected $affiliate = 0;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 */
	protected $token;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	protected $tokenExpiresAt;

	/**
	 * @ORM\Column(type="boolean")
	 */
	protected $admin = FALSE;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $adminDescription = '';

	/**
	 * @ORM\OneToMany(targetEntity="AdminPermission", mappedBy="user", cascade={"persist", "remove"})
	 */
	protected $adminPermissions;

	/**
	 * @ORM\OneToMany(targetEntity="\Teddy\Entities\Coins\CoinSack", mappedBy="user", cascade={"persist", "remove"})
	 * @ORM\OrderBy({"expiresAt" = "ASC"})
	 * @var CoinSack[]|ArrayCollection
	 */
	protected $coinSacks;

	/** Token for changing password, expiration in hours */
	const TOKEN_EXPIRATION = 1;



	public function __construct()
	{
		parent::__construct();
		$this->verificationCode = mt_rand(1000000, 9999999);
		$this->affiliate = mt_rand(1000000, 9999999);
		$this->registeredAt = new \DateTime();
		$this->lastLoginAt = new \DateTime("@0");
		$this->lastActivityAt = new \DateTime("@0");
		$this->adminPermissions = new ArrayCollection;
		$this->coinSacks = new ArrayCollection();
	}



	/**
	 * Generates token
	 *
	 * @return int
	 */
	public function generateToken()
	{
		$expiration = new \DateTime();
		$expiration->setTimestamp(time() + self::TOKEN_EXPIRATION * 3600);
		$this->token = mt_rand(100000000, 999999999);
		$this->tokenExpiresAt = $expiration;
		return $this->token;
	}



	/**
	 * Invalidates token
	 */
	public function invalidateToken()
	{
		$this->tokenExpiresAt = new \DateTime();
	}



	/**
	 * Returns email, hides inner letters if required
	 *
	 * @param bool $hide
	 * @return string
	 */
	public function getEmail($hide = FALSE)
	{
		if (!$hide) {
			return $this->email;
		} else {
			$parts = explode('@', $this->email, 2);
			if (strlen($parts[0]) > 2) {
				$parts[0] = substr($parts[0], 0, 1) . str_repeat('*', strlen($parts[0]) - 2) . substr($parts[0], -1);
			}
			return $parts[0] . '@' . $parts[1];
		}
	}



	/**
	 * @return bool
	 */
	public function isAdmin()
	{
		return $this->admin;
	}



	/**
	 * Checks if user is allowed in $presenter in AdminModule
	 *
	 * @param string $presenter
	 * @return bool
	 */
	public function isAllowed($presenter)
	{
		foreach ($this->adminPermissions as $permission) {
			if ($permission->getPresenter() == $presenter) {
				return TRUE;
			}
		}

		return FALSE;
	}



	/**
	 * @param bool $array
	 * @return array|ArrayCollection
	 */
	public function getAdminPermissions($array = FALSE)
	{
		if (!$array) {
			return $this->adminPermissions;
		} else {
			$adminPermissions = [];
			foreach ($this->adminPermissions as $adminPermission) {
				$adminPermissions[] = $adminPermission->getPresenter();
			}
			return $adminPermissions;
		}
	}



	/**
	 * @param User $user
	 * @return bool
	 */
	public function canEdit(User $user)
	{
		return ($user === $this || $user->isAdmin());
	}



	/**
	 * @param \DateTime $lastLoginAt
	 * @return User
	 */
	public function setLastLoginAt(\DateTime $lastLoginAt)
	{
		$this->lastLoginAt = $lastLoginAt;
		return $this;
	}



	/**
	 * @return int
	 */
	public function getTotalUsableCoins()
	{
		$total = 0;
		foreach ($this->coinSacks as $coinSack) {
			if (!$coinSack->isExpired()) {
				$total += $coinSack->getRemaining();
			}
		}
		return $total;
	}


	/**
	 * @param CoinSack $coinSack
	 */
	public function addCoinSack(CoinSack $coinSack)
	{
		if (!$this->coinSacks->contains($coinSack)) {
			$this->coinSacks->add($coinSack);
		}
		$this->orderCoinSacks();
	}


	/**
	 * @return ArrayCollection|Entities\Coins\CoinSack[]
	 */
	public function getCoinSacks()
	{
		return new \Kdyby\Doctrine\Collections\ReadOnlyCollectionWrapper($this->coinSacks);
	}


	/**
	 * Orders CoinSacks by expiratesAt
	 * CoinSacks do not have to be sorted when added
	 */
	public function orderCoinSacks()
	{
		$iterator = $this->coinSacks->getIterator();
		$iterator->uasort(function (CoinSack $a, CoinSack $b) {
			return $a->getExpiresAt() < $b->getExpiresAt() ? -1 : 1;
		});
		$this->coinSacks = new ArrayCollection(iterator_to_array($iterator));
	}

}
