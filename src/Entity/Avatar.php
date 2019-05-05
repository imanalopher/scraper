<?php


namespace App\Entity;

use Doctrine\ORM\Mapping AS ORM;

/**
 * Class Avatar
 * @package App\Entity
 *
 * @ORM\Table(name="avatar")
 * @ORM\Entity()
 */
class Avatar extends Entity
{
    /**
     * @var string
     * @ORM\Column(name="img", type="string", nullable=true)
     */
    private $img;

    /**
     * @var string
     * @ORM\Column(name="url", type="string", nullable=true)
     */
    private $url;

    /**
     * @var boolean
     * @ORM\Column(name="access", type="boolean", nullable=false)
     */
    private $access = true;

    /**
     * @var int
     * @ORM\Column(name="status", type="smallint", nullable=false)
     */
    private $status;

    /**
     * @var Provider
     * @ORM\OneToOne(targetEntity="Provider", inversedBy="avatar")
     * @ORM\JoinColumn(name="provider_id", referencedColumnName="id")
     */
    private $provider;

    /**
     * @return string
     */
    public function getImg()
    {
        return $this->img;
    }

    /**
     * @param string $img
     */
    public function setImg(string $img)
    {
        $this->img = $img;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url)
    {
        $this->url = $url;
    }

    /**
     * @return bool
     */
    public function isAccess()
    {
        return $this->access;
    }

    /**
     * @param bool $access
     */
    public function setAccess(bool $access)
    {
        $this->access = $access;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status)
    {
        $this->status = $status;
    }

    /**
     * @return Provider
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * @param Provider $provider
     */
    public function setProvider(Provider $provider)
    {
        $this->provider = $provider;
    }
}