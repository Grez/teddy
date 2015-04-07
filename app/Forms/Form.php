<?php

namespace Teddy\Forms;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;
use Kdyby\Doctrine\Entities\BaseEntity;
use Nette\Forms\Container;

class Form extends \Nette\Application\UI\Form
{

    /** @var BaseEntity */
    protected $entity;


    /**
     * @param BaseEntity $entity
     */
    public function bindEntity(BaseEntity $entity)
    {
        $this->entity = $entity;
        $this->fillComponents($this->getComponents());
    }

    /**
     * Fills form's components with data from entity
     * @param \ArrayIterator $components
     */
    protected function fillComponents($components)
    {
        foreach ($components as $name => $input) {
            if ($input instanceof Container) {
                $this->fillComponents($input->getComponents());
            } else {
                try {
                    $method = "get$name";
                    $value = $this->entity->$method();
                } catch (\Kdyby\Doctrine\MemberAccessException $e) {
                    continue;
                }

                $value = $this->entity->$method();

                if ($value instanceof BaseEntity) {
                    $value = $value->getId();
                } elseif ($value instanceof ArrayCollection || $value instanceof PersistentCollection) {
                    $value = array_map(function (BaseEntity $entity) {
                        return $entity->getId();
                    }, $value->toArray());
                }

                if ($value instanceof \DateTime) {
                    $value = $value->format('Y-m-d H:i:s');
                }
                $input->setDefaultValue($value);
            }
        }
    }

}