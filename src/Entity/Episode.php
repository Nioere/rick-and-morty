<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EpisodeRepository")
 */
class Episode
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $air_date;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $episode;

    /**
     * @ORM\ManyToMany(targetEntity="Character", inversedBy="episodes")
     * @ORM\JoinTable(name="characters_episodes")
     */
    private $characters;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $url;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;

    public function __construct()
    {
        $this->characters = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getAirDate()
    {
        return $this->air_date;
    }

    public function getEpisode()
    {
        return $this->episode;
    }

    public function getCharacters()
    {
        return $this->characters;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getCreated()
    {
        return $this->created;
    }


    public function setName($name)
    {
        $this->name = $name;
    }

    public function setAirDate($air_date)
    {
        $this->air_date = $air_date;
    }

    public function setEpisode($episode)
    {
        $this->episode = $episode;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function setCreated($created)
    {
        $this->created = $created;
    }
}
