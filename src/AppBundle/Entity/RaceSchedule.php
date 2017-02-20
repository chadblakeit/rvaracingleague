<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RaceScheduleRepository")
 * @ORM\Table(name="race_schedule")
 */
class RaceSchedule
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="racename", type="string")
     */
    protected $racename;

    /**
     * @ORM\Column(name="racedate", type="datetime")
     */
    protected $racedate;

    /**
     * @ORM\Column(name="qualifyingdate", type="datetime")
     */
    protected $qualifyingdate;

    /**
     * @ORM\Column(name="activerace", type="smallint")
     */
    protected $activerace;

    /**
     * @ORM\Column(name="location", type="string")
     */
    protected $location;

    /**
     * @ORM\Column(name="trackname", type="string")
     */
    protected $trackname;

    /**
     * @ORM\Column(name="tracklength", type="decimal", precision=5, scale=4)
     */
    protected $tracklength;

    /**
     * @ORM\Column(name="logo", type="string")
     */
    protected $logo;

    /**
     * @ORM\OneToMany(targetEntity="RaceSubmissions", mappedBy="race")
     */
    protected $raceSubmissions;

    /**
     * @ORM\OneToMany(targetEntity="RaceResults", mappedBy="race")
     */
    protected $raceResults;

    /**
     * @return mixed
     */
    public function getRacename()
    {
        return $this->racename;
    }

    /**
     * @param mixed $racename
     */
    public function setRacename($racename)
    {
        $this->racename = $racename;
    }

    /**
     * @return mixed
     */
    public function getRacedate()
    {
        return $this->racedate;
    }

    /**
     * @param mixed $racedate
     */
    public function setRacedate($racedate)
    {
        $this->racedate = $racedate;
    }

    /**
     * @return mixed
     */
    public function getQualifyingdate()
    {
        return $this->qualifyingdate;
    }

    /**
     * @param mixed $qualifyingdate
     */
    public function setQualifyingdate($qualifyingdate)
    {
        $this->qualifyingdate = $qualifyingdate;
    }

    /**
     * @return mixed
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param mixed $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @return mixed
     */
    public function getTrackname()
    {
        return $this->trackname;
    }

    /**
     * @param mixed $trackname
     */
    public function setTrackname($trackname)
    {
        $this->trackname = $trackname;
    }

    /**
     * @return mixed
     */
    public function getTracklength()
    {
        return $this->tracklength;
    }

    /**
     * @param mixed $tracklength
     */
    public function setTracklength($tracklength)
    {
        $this->tracklength = $tracklength;
    }

    /**
     * @return mixed
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * @param mixed $logo
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;
    }

    /**
     * @return mixed
     */
    public function getActiverace()
    {
        return $this->activerace;
    }

    /**
     * @param mixed $activerace
     */
    public function setActiverace($activerace)
    {
        $this->activerace = $activerace;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getRaceSubmissions()
    {
        return $this->raceSubmissions;
    }

    /**
     * @param mixed $teamSubmissions
     */
    public function setRaceSubmissions($raceSubmissions)
    {
        $this->raceSubmissions = $raceSubmissions;
    }



}