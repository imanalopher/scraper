<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Specialties
 * @package App\Entity
 *
 * @ORM\Table(name="specialties")
 * @ORM\Entity()
 */
class Specialties extends Entity
{
    /**
     * @var string
     * @ORM\Column(name="specialty", type="string", options={"comment": "Specialty of Provider"})
     */
    private $specialty;

    /**
     * @var Provider
     *
     * @ORM\ManyToOne(targetEntity="Provider", inversedBy="specialties")
     */
    private $provider;

    /**
     * @return string
     */
    public function getSpecialty()
    {
        return $this->specialty;
    }

    /**
     * @param string $specialty
     */
    public function setSpecialty($specialty)
    {
        $this->specialty = $specialty;
    }

    /**
     * @return Provider
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * @param mixed $provider
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;
    }
}