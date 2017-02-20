<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RaceResultsRepository")
 * @ORM\Table(name="race_results")
 */
class RaceResults
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="RaceSchedule", inversedBy="raceResults")
     * @ORM\JoinColumn(name="race_id", referencedColumnName="id", nullable=false)
     */
    protected $race;

    /**
     * @ORM\Column(name="results", type="json_array")
     */
    protected $results;

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
    public function getResults()
    {
        return $this->results;
    }

    /**
     * @param mixed $results
     */
    public function setResults($results)
    {
        $this->results = $results;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

}