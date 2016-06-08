<?php

namespace Teddy\Entities\User;

use Kdyby\Doctrine\Collections\ReadOnlyCollectionWrapper;
use Nette;
use Nette\Security\Passwords;
use Nette\Utils\Random;
use Teddy\Entities;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Teddy\Entities\Coins\CoinSack;
use Teddy\Images\WithImage;
use Teddy\Images\ImageService;



/**
 * @ORM\MappedSuperclass()
 */
abstract class Player extends \Kdyby\Doctrine\Entities\BaseEntity implements WithImage
{

	/**
	 * @ORM\Column(type="string", unique=true)
	 * @var string
	 */
	protected $nick;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	protected $email;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	protected $password;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	protected $name = '';

	/**
	 * @ORM\Column(type="boolean")
	 * @var bool
	 */
	protected $emailVerified = FALSE;

	/**
	 * @ORM\Column(type="integer")
	 * @var int
	 * Generated in __construct()
	 */
	protected $verificationCode = 0;

	/**
	 * @ORM\Column(type="smallint")
	 * @var int
	 */
	protected $age = 0;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	protected $location = '';

	/**
	 * @ORM\Column(type="smallint")
	 * @var int
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
	 * @ORM\Column(type="string", nullable=TRUE)
	 * @var string
	 */
	protected $avatar;

	/**
	 * @ORM\Column(type="boolean", nullable=FALSE)
	 * @var bool
	 */
	protected $deleted = FALSE;

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
	 * @ORM\Column(type="string", nullable=true)
	 * @var string
	 */
	protected $apiKey;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 * @var \DateTime
	 */
	protected $tokenExpiresAt;

	/**
	 * @ORM\OneToMany(targetEntity="\Game\Entities\Coins\CoinSack", mappedBy="user", cascade={"persist", "remove"})
	 * @ORM\OrderBy({"expiresAt" = "ASC"})
	 * @var CoinSack[]|ArrayCollection
	 */
	protected $coinSacks;

	/** Token for changing password, expiration in hours */
	const TOKEN_EXPIRATION = 1;



	/**
	 * @param string $email
	 * @param string $nick
	 * @param string $password
	 */
	public function __construct($email, $nick = '', $password = '')
	{
		parent::__construct();
		$this->email = $email;
		$this->nick = $nick ?: $email;
		$this->password = $password ?: Passwords::hash(Random::generate());
		$this->apiKey = Random::generate();
		$this->verificationCode = mt_rand(1000000, 9999999);
		$this->affiliate = mt_rand(1000000, 9999999);
		$this->registeredAt = new \DateTime();
		$this->lastLoginAt = new \DateTime("@0");
		$this->lastActivityAt = new \DateTime("@0");
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
	 * @return string
	 */
	public function getEmail()
	{
		return $this->email;
	}



	/**
	 * Hides inner letters (e.g. a***n@admin.cz)
	 *
	 * @return string
	 */
	public function getAnonymizedEmail()
	{
		$parts = explode('@', $this->getEmail(), 2);
		if (strlen($parts[0]) > 2) {
			$parts[0] = substr($parts[0], 0, 1) . str_repeat('*', strlen($parts[0]) - 2) . substr($parts[0], -1);
		}
		return $parts[0] . '@' . $parts[1];
	}



	/**
	 * @return bool
	 */
	public function isAdmin()
	{
		return ($this instanceof \Game\Entities\User\Admin);
	}



	/**
	 * @param Player $user
	 * @return bool
	 */
	public function canEdit(Player $user)
	{
		return ($user === $this || $user->isAdmin());
	}



	/**
	 * @param \DateTime $lastLoginAt
	 * @return Player
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
		return new ReadOnlyCollectionWrapper($this->coinSacks);
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



	/**
	 * @param bool $admin
	 * @return Player
	 */
	public function setAdmin($admin)
	{
		$this->admin = $admin;
		return $this;
	}



	/**
	 * @return string
	 */
	public function getApiKey()
	{
		return $this->apiKey;
	}



	/**
	 * @param string $email
	 * @return Player
	 */
	public function setEmail($email)
	{
		$this->email = $email;
		return $this;
	}



	/**
	 * @return string
	 */
	public function getAvatar()
	{
		return $this->avatar;
	}



	/**
	 * @param string $avatar
	 * @return Player
	 */
	public function setAvatar($avatar)
	{
		$this->avatar = $avatar;
		return $this;
	}



	/**
	 * @return bool
	 */
	public function hasAvatar()
	{
		return $this->avatar !== NULL;
	}



	/**
	 * Deletes User's avatar
	 *
	 * @param ImageService $imageService
	 * @return $this
	 */
	public function deleteAvatar(ImageService $imageService)
	{
		@unlink($imageService->getAvatarPath($this->avatar));
		$this->avatar = NULL;
		return $this;
	}



	/**
	 * @return string
	 */
	public function getPassword()
	{
		return $this->password;
	}



	/**
	 * @param ImageService $imageService
	 * @param string|null $variant
	 * @return string
	 */
	public function resolveImage(ImageService $imageService, $variant = NULL)
	{
		return $imageService->getAvatar($this->getAvatar());
	}



	/**
	 * @param boolean $deleted
	 * @return Player
	 */
	public function setDeleted($deleted)
	{
		$this->deleted = $deleted;
		return $this;
	}



	/**
	 * @return boolean
	 */
	public function isDeleted()
	{
		return $this->deleted;
	}



	/**
	 * @param \DateTime $lastActivityAt
	 * @return Player
	 */
	public function setLastActivityAt($lastActivityAt)
	{
		$this->lastActivityAt = $lastActivityAt;
		return $this;
	}



	/**
	 * @return string
	 */
	public function getNick()
	{
		return $this->nick;
	}



	/**
	 * @param string $nick
	 * @return Player
	 */
	public function setNick($nick)
	{
		$this->nick = $nick;
		return $this;
	}

}
