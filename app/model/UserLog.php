<?php

namespace Teddy\Model;

use Doctrine\ORM\Mapping as ORM;
use Nette;

/**
 * @ORM\Entity
 */
class UserLog extends \Kdyby\Doctrine\Entities\BaseEntity
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
     * @ORM\Column(type="integer")
     * 0 - 1000 reserved for Teddy
     */
    protected $action = 0;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $type = 0;

    /**
     * @ORM\Column(type="array")
     */
    protected $data;

    /**
     * @ORM\Column(type="datetime")
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
        $data = (is_array($this->getData())) ? $this->getData() : array($this->getData());
        return vsprintf($this->getTemplate(), $data);
    }

    /**
     * @return string
     * @throws \TeddyException
     */
    protected function getTemplate()
    {
        $templates = array(
            self::ADMIN => array(
                self::ADMIN_DAEMON => 'Daemon %s',
                self::ADMIN_EDIT_USER => 'Edited user %s',
                self::ADMIN_DELETE_USER => 'Deleted user %s',
                self::ADMIN_BAN_USER => 'Banned user %s',
                self::ADMIN_CREATE_ADMIN => 'Created admin %s',
                self::ADMIN_EDIT_ADMIN => 'Edited admin %s',
                self::ADMIN_DELETE_ADMIN => 'Deleted admin %s',
            ),
            self::USER => array(
                self::USER_CHANGE_PASSWORD => 'Changed password',
                self::USER_DELETE_USER => 'Deleted profile',
            ),
            self::GAME => array(
            ),
        );

        if (isset($templates[$this->type][$this->action])) {
            return $templates[$this->type][$this->action];
        }

        throw new \TeddyException('Unknown action ' . $this->type . ':' . $this->action);
    }

}