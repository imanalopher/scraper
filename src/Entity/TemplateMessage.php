<?php


namespace App\Entity;

use Doctrine\ORM\Mapping AS ORM;

/**
 * Class TemplateMessage
 * @package App\Entity
 *
 * @ORM\Table(name="template_message")
 * @ORM\Entity()
 */
class TemplateMessage extends Entity
{
    /**
     * @var string
     * @ORM\Column(name="tag", type="string", nullable=true)
     */
    private $tag;

    /**
     * @var string
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

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

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
    }
}