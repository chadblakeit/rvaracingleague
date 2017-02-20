<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RaceSubmissionsRepository")
 * @ORM\Table(name="race_submissions")
 */
class RaceSubmissions
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="raceSubmissions")
     * @ORM\JoinColumn(name="fos_user", referencedColumnName="id")
     */
    protected $fos_user;

    /**
     * @ORM\ManyToOne(targetEntity="League", inversedBy="raceSubmissions")
     * @ORM\JoinColumn(name="league_id", referencedColumnName="id")
     */
    protected $league;

    /**
     * @ORM\ManyToOne(targetEntity="RaceSchedule", inversedBy="raceSubmissions")
     * @ORM\JoinColumn(name="race_id", referencedColumnName="id")
     */
    protected $race;

    /**
     * @ORM\Column(name="drivers", type="json_array")
     */
    protected $drivers;

    /**
     * @ORM\Column(name="created", type="datetime")
     */
    protected $created;

    /**
     * @return mixed
     */
    public function getFosUser()
    {
        return $this->fos_user;
    }

    /**
     * @param mixed $fos_user
     */
    public function setFosUser($fos_user)
    {
        $this->fos_user = $fos_user;
    }

    /**
     * @return mixed
     */
    public function getLeague()
    {
        return $this->league;
    }

    /**
     * @param mixed $league
     */
    public function setLeague($league)
    {
        $this->league = $league;
    }

    /**
     * @return mixed
     */
    public function getRace()
    {
        return $this->race;
    }

    /**
     * @param mixed $race
     */
    public function setRace($race)
    {
        $this->race = $race;
    }

    /**
     * @return mixed
     */
    public function getDrivers()
    {
        return $this->drivers;
    }

    /**
     * @param mixed $drivers
     */
    public function setDrivers($drivers)
    {
        $this->drivers = $drivers;
    }

    /**
     * @return mixed
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param mixed $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }
}