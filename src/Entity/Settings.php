<?php

namespace App\Entity;

use Doctrine\ORM\Mapping AS ORM;

/**
 * Class Settings
 * @package App\Entity
 *
 * @ORM\Entity()
 * @ORM\Table(name="settings")
 */
class Settings extends Entity
{
    /**
     * @var string
     * @ORM\Column(name="name", type="string", nullable=true, options={"comment":"Setting name."})
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(name="title", type="string", nullable=true)
     */
    private $title;

    /**
     * @var string
     * @ORM\Column(name="tag", type="string", nullable=true)
     */
    private $tag;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @param string $tag
     */
    public function setTag(string $tag)
    {
        $this->tag = $tag;
    }
}