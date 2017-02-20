<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_leagues")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserLeaguesRepository")
 */
class UserLeagues
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="userLeagues")
     * @ORM\JoinColumn(name="fos_user", referencedColumnName="id")
     */
    protected $fos_user;

    /**
     * @ORM\ManyToOne(targetEntity="League", inversedBy="userLeagues")
     * @ORM\JoinColumn(name="league_id", referencedColumnName="id")
     */
    protected $league;

    /**
     * @return mixed
     */
    public function getLeague()
    {
        return $this->league;
    }

    /**
     * @param mixed $league_id
     */
    public function setLeague($league)
    {
        $this->league = $league;
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
