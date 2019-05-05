<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping AS ORM;

/**
 * Class Provider
 *
 * @ORM\Entity()
 * @ORM\Table(name="provider", indexes={
 *      @ORM\Index(name="provider_id_idx", columns={"provider_id"}),
 *      @ORM\Index(name="search_idx", columns={"first_name", "last_name"})
 * })
 */
class Provider extends Entity
{
    /**
     * @var integer
     * @ORM\Column(name="provider_id", type="integer", nullable=false)
     */
    private $providerId;

    /**
     * @var string
     * @ORM\Column(name="first_name", type="string", nullable=true)
     */
    private $firstName;

    /**
     * @var string
     * @ORM\Column(name="last_name", type="string", nullable=true)
     */
    private $lastName;

    /**
     * @var string
     * @ORM\Column(name="middle_name", type="string", nullable=true)
     */
    private $middleName;

    /**
     * @var \DateTime
     * @ORM\Column(name="birthdate", type="date", nullable=true)
     */
    private $birthdate;

    /**
     * @var string
     * @ORM\Column(name="email", type="string", nullable=true)
     */
    private $email;

    /**
     * @var array
     * @ORM\Column(name="mailing_address", type="array", nullable=true)
     */
    private $mailingAddress;

    /**
     * @var array
     * @ORM\Column(name="phones", type="array", nullable=true)
     */
    private $phones;

    /**
     * @var string
     * @ORM\Column(name="city", type="string", nullable=true)
     */
    private $city;

    /**
     * @var string
     * @ORM\Column(name="state", type="string", nullable=true)
     */
    private $state;

    /**
     * @var string
     * @ORM\Column(name="zip", type="string", nullable=true)
     */
    private $zip;

    /**
     * @var string
     * @ORM\Column(name="timezone", type="string", nullable=true)
     */
    private $timezone;

    /**
     * @var Specialties[]
     *
     * @ORM\OneToMany(targetEntity="Specialties", mappedBy="provider")
     */
    private $specialties;

    /**
     * @var Avatar
     * @ORM\OneToOne(targetEntity="Avatar", mappedBy="provider")
     */
    private $avatar;

    public function __construct()
    {
        $this->specialties = new ArrayCollection();
        $this->mailingAddress = [];
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getProviderId()
    {
        return $this->providerId;
    }

    /**
     * @param int $providerId
     */
    public function setProviderId(int $providerId)
    {
        $this->providerId = $providerId;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getMiddleName()
    {
        return $this->middleName;
    }

    /**
     * @param string $middleName
     */
    public function setMiddleName($middleName)
    {
        $this->middleName = $middleName;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return array
     */
    public function getPhones()
    {
        return $this->phones;
    }

    /**
     * @param array $phones
     */
    public function setPhones($phones)
    {
        $this->phones = $phones;
    }

    /**
     * @param string $phone
     */
    public function addPhone($phone)
    {
        $this->phones[] = $phone;
    }

    /**
     * @return \DateTime
     */
    public function getBirthdate()
    {
        return $this->birthdate;
    }

    /**
     * @param \DateTime $birthdate
     */
    public function setBirthdate($birthdate)
    {
        $this->birthdate = $birthdate;
    }

    /**
     * @return array
     */
    public function getMailingAddress()
    {
        return $this->mailingAddress;
    }

    /**
     * @param array $mailingAddress
     */
    public function setMailingAddress($mailingAddress)
    {
        $this->mailingAddress = $mailingAddress;
    }

    /**
     * @param string $mailingAddress
     */
    public function addMailingAddress($mailingAddress)
    {
        $this->mailingAddress[] = $mailingAddress;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param string $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @return string
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * @param string $zip
     */
    public function setZip($zip)
    {
        $this->zip = $zip;
    }

    /**
     * @return string
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * @param string $timezone
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;
    }

    /**
     * @return Avatar
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * @param Avatar $avatar
     */
    public function setAvatar(Avatar $avatar)
    {
        $this->avatar = $avatar;
    }

}