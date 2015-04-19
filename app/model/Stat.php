<?php

namespace Teddy\Model;

use Doctrine\ORM\Mapping as ORM;
use Nette;

/**
 * @ORM\Entity
 */
class Stat extends \Kdyby\Doctrine\Entities\BaseEntity
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(type="datetime")
     * Generated in __construct()
     */
    protected $date;

    /**
     * @ORM\Column(type="integer")
     */
    protected $players;

    /**
     * @ORM\Column(type="integer")
     */
    protected $playersActive;

    /**
     * @ORM\Column(type="integer")
     */
    protected $playersOnline;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    protected $load1;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    protected $load5;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    protected $load15;


    public function __construct()
    {
        $this->date = new \DateTime();
    }

}