<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="renew_league")
 */
class RenewLeague
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="renewLeague")
     * @ORM\JoinColumn(name="fos_user", referencedColumnName="id")
     */
    protected $fos_user;

    /**
     * @ORM\ManyToOne(targetEntity="League", inversedBy="renewLeague")
     * @ORM\JoinColumn(name="league_id", referencedColumnName="id")
     */
    private $league;

    /**
     * @ORM\Column(name="season", type="string")
     * @Assert\NotBlank()
     */
    protected $season;

    /**
     * @ORM\Column(name="renewed", type="smallint")
     */
    protected $renewed;

    /**
     * @ORM\Column(name="declined", type="smallint", options={"default" : 0})
     */
    protected $declined;

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
    public function getSeason()
    {
        return $this->season;
    }

    /**
     * @param mixed $season
     */
    public function setSeason($season)
    {
        $this->season = $season;
    }

    /**
     * @return mixed
     */
    public function getRenewed()
    {
        return $this->renewed;
    }

    /**
     * @param mixed $renewed
     */
    public function setRenewed($renewed)
    {
        $this->renewed = $renewed;
    }

    /**
     * @return mixed
     */
    public function getDeclined()
    {
        return $this->declined;
    }

    /**
     * @param mixed $declined
     */
    public function setDeclined($declined)
    {
        $this->declined = $declined;
    }

}