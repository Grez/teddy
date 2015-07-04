<?php

namespace Teddy\Model;

use Doctrine\ORM\Mapping as ORM;
use Nette;

/**
 * @ORM\Entity
 */
class StatDaily extends \Kdyby\Doctrine\Entities\BaseEntity
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(type="date", unique=true)
     */
    protected $date;

    /**
     * @ORM\Column(type="integer")
     */
    protected $playersTotal = 0;

    /**
     * @ORM\Column(type="integer")
     */
    protected $playersActive = 0;

    /**
     * @ORM\Column(type="integer")
     */
    protected $playersOnline = 0;


    public function setPlayersTotal($playersTotal)
    {
        $this->playersTotal = intVal($playersTotal);
    }

    public function setPlayersActive($playersActive)
    {
        $this->playersActive = intVal($playersActive);
    }

    public function setPlayersOnline($playersOnline)
    {
        $this->playersOnline = intVal($playersOnline);
    }

}
