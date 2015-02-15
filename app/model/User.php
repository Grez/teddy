<?php

namespace App\Model;

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
    protected $created;

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


    public function __construct()
    {
        parent::__construct();
        $this->verificationCode = mt_rand(1000000, 9999999);
        $this->affiliate = mt_rand(1000000, 9999999);
        $this->created = new \DateTime();
    }


}
