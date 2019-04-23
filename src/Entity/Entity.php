<?php

namespace App\Entity;

use Doctrine\ORM\Mapping AS ORM;
use App\Entity\IEntity\InterfaceEntity;

abstract class Entity implements InterfaceEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    public $id;

    public function getId()
    {
        return $this->id;
    }
}