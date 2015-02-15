<?php

namespace App\Model;

use Doctrine\ORM\Mapping as ORM;
use Nette;

/**
 * @ORM\Entity
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
     * Set in __construct()
     */
    protected $userAgent = '';

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
     * @ORM\Column(type="datetime")
     * Set in __construct()
     */
    protected $date;


    public function __construct()
    {
        $this->date = new \DateTime();
        $this->userAgent = $_SERVER['HTTP_USER_AGENT'];
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