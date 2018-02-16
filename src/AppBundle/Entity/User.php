<?php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="firstname", type="string")
     * @Assert\NotBlank()
     */
    protected $firstname;

    /**
     * @ORM\Column(name="lastname", type="string")
     * @Assert\NotBlank()
     */
    protected $lastname;

    /**
     * @ORM\OneToMany(targetEntity="RaceSubmissions", mappedBy="fos_user")
     */
    private $raceSubmissions;

    /**
     * @ORM\OneToMany(targetEntity="UserLeagues", mappedBy="fos_user")
     */
    private $userLeagues;

    /**
     * @ORM\OneToMany(targetEntity="League", mappedBy="fos_user")
     */
    private $league;

    /**
     * @ORM\OneToMany(targetEntity="RenewLeague", mappedBy="fos_user")
     */
    private $renewLeague;

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

    public function __construct()
    {
        parent::__construct();

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

    /**
     * @return mixed
     */
    public function getUserLeagues()
    {
        return $this->userLeagues;
    }

    /**
     * @param mixed $userLeagues
     */
    public function setUserLeagues($userLeagues)
    {
        $this->userLeagues = $userLeagues;
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



}
