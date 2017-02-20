<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DriversRepository")
 * @ORM\Table(name="drivers")
 */
class Drivers
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="firstname", type="string", length=30)
     */
    protected $firstname;

    /**
     * @ORM\Column(name="lastname", type="string", length=40)
     */
    protected $lastname;

    /**
     * @ORM\Column(name="number", type="integer", scale=2)
     */
    protected $number;

    /**
     * @ORM\Column(name="sponsor", type="string", length=100)
     */
    protected $sponsor;

    /**
     * @ORM\Column(name="team", type="string", length=40)
     */
    protected $team;

    /**
     * @ORM\Column(name="carmake", type="string", length=20)
     */
    protected $carmake;

    /**
     * @ORM\Column(name="inactive", type="smallint", options={"default" : 0})
     */
    protected $inactive;

    /**
     * @return mixed
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param mixed $firstname
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     * @return mixed
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param mixed $lastname
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    /**
     * @return mixed
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param mixed $number
     */
    public function setNumber($number)
    {
        $this->number = $number;
    }

    /**
     * @return mixed
     */
    public function getSponsor()
    {
        return $this->sponsor;
    }

    /**
     * @param mixed $sponsor
     */
    public function setSponsor($sponsor)
    {
        $this->sponsor = $sponsor;
    }

    /**
     * @return mixed
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * @param mixed $team
     */
    public function setTeam($team)
    {
        $this->team = $team;
    }

    /**
     * @return mixed
     */
    public function getCarmake()
    {
        return $this->carmake;
    }

    /**
     * @param mixed $carmake
     */
    public function setCarmake($carmake)
    {
        $this->carmake = $carmake;
    }

    /**
     * @return mixed
     */
    public function getInactive()
    {
        return $this->inactive;
    }

    /**
     * @param mixed $inactive
     */
    public function setInactive($inactive)
    {
        $this->inactive = $inactive;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }



}