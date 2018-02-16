<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="leagues")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LeagueRepository")
 */
class League
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="league")
     * @ORM\JoinColumn(name="fos_user", referencedColumnName="id")
     */
    protected $fos_user;

    /**
     * @ORM\Column(name="name", type="string")
     */
    protected $name;

    /**
     * @ORM\Column(name="confirmation", type="string")
     */
    protected $confirmation;

    /**
     * @ORM\Column(name="active", type="smallint", options={"default" : 0})
     */
    protected $active;

    /**
     * @ORM\Column(name="created", type="datetime")
     */
    protected $created;

    /**
     * @ORM\Column(name="disabled", type="smallint", options={"default" : 0})
     */
    protected $disabled;

    /**
     * @ORM\OneToMany(targetEntity="InviteUser", mappedBy="league")
     */
    private $inviteUsers;

    /**
     * @ORM\OneToMany(targetEntity="UserLeagues", mappedBy="league")
     */
    private $userLeagues;

    /**
     * @ORM\OneToMany(targetEntity="RaceSubmissions", mappedBy="league")
     */
    private $raceSubmissions;

    /**
     * @ORM\OneToMany(targetEntity="RenewLeague", mappedBy="league")
     */
    private $renewLeague;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param mixed $active
     */
    public function setActive($active)
    {
        $this->active = $active;
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
    public function getDisabled()
    {
        return $this->disabled;
    }

    /**
     * @param mixed $disabled
     */
    public function setDisabled($disabled)
    {
        $this->disabled = $disabled;
    }

    /**
     * @return mixed
     */
    public function getConfirmation()
    {
        return $this->confirmation;
    }

    /**
     * @param mixed $confirmation
     */
    public function setConfirmation($confirmation)
    {
        $this->confirmation = $confirmation;
    }



    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    public function __construct()
    {
        $this->created = new \DateTime();
        //$this->inviteUsers = new ArrayCollection();

        //$exdate = new \DateTime();
        //$exdate->add(new \DateInterval('P2D'));
        //$this->expire = $exdate;
    }

    /**
     * @return mixed
     */
    public function getInviteUsers()
    {
        return $this->inviteUsers;
    }

    /**
     * @param mixed $inviteUsers
     */
    public function setInviteUsers($inviteUsers)
    {
        $this->inviteUsers = $inviteUsers;
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
    public function getRaceSubmissions()
    {
        return $this->raceSubmissions;
    }

    /**
     * @param mixed $raceSubmissions
     */
    public function setRaceSubmissions($raceSubmissions)
    {
        $this->raceSubmissions = $raceSubmissions;
    }

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



}
