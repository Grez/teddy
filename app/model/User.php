<?php

namespace Teddy\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Nette;

/**
 * @ORM\Entity
 */
class User extends \Kdyby\Doctrine\Entities\BaseEntity
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    protected $nick;

    /**
     * @ORM\Column(type="string")
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
     * @ORM\Column(type="datetime", nullable=true))
     */
    protected $lastLogin;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $lastActivity;

    /**
     * @ORM\Column(type="datetime")
     * Generated in __construct()
     */
    protected $registered;

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
    protected $active = 0;

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
    protected $tokenExpiration;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $admin = false;

    /**
     * @ORM\Column(type="string")
     */
    protected $adminDescription = '';

    /**
     * @ORM\OneToMany(targetEntity="AdminPermission", mappedBy="user", cascade={"persist", "remove"})
     */
    protected $adminPermissions;


    /** Token for changing password, expiration in hours */
    const TOKEN_EXPIRATION = 1;


    public function __construct()
    {
        parent::__construct();
        $this->verificationCode = mt_rand(1000000, 9999999);
        $this->affiliate = mt_rand(1000000, 9999999);
        $this->registered = new \DateTime();
        $this->adminPermissions = new ArrayCollection;
    }

    /**
     * Generates token
     * @return int
     */
    public function generateToken()
    {
        $expiration = new \DateTime();
        $expiration->setTimestamp(time() + self::TOKEN_EXPIRATION * 3600);
        $this->token = mt_rand(100000000, 999999999);
        $this->tokenExpiration = $expiration;
        return $this->token;
    }

    /**
     * Invalidates token
     */
    public function invalidateToken()
    {
        $this->tokenExpiration = new \DateTime();
    }

    /**
     * Returns email, hides inner letters if required
     * @param bool $hide
     * @return string
     */
    public function getEmail($hide = false)
    {
        if(!$hide) {
            return $this->email;
        } else {
            $parts = explode('@', $this->email, 2);
            if(strlen($parts[0]) > 2) {
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
     * @param string $presenter
     * @return bool
     */
    public function isAllowed($presenter)
    {
        if ($presenter == 'Admin:Main') {
            return true;
        }

        foreach ($this->adminPermissions as $permission) {
            if ('Admin:' . $permission->getPresenter() == $presenter) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param bool $array
     * @return array|ArrayCollection
     */
    public function getAdminPermissions($array = false)
    {
        if (!$array) {
            return $this->adminPermissions;
        } else {
            $adminPermissions = array();
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
}
