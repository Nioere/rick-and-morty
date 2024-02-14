<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 */
class Character
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
     * @ORM\Column(type="string", length=50)
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $species;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $gender;

    /**
     * @ORM\ManyToOne(targetEntity="Location", inversedBy="originCharacters")
     */
    private $origin;

    /**
     * @ORM\ManyToOne(targetEntity="Location", inversedBy="locationCharacters")
     */
    private $location;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $image;

    /**
     * @ORM\ManyToMany(targetEntity="Episode", mappedBy="characters")
     */
    private $episodes;

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
        $this->episodes = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getSpecies()
    {
        return $this->species;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getGender()
    {
        return $this->gender;
    }

    public function getOrigin()
    {
        return $this->origin;
    }

    public function getLocation()
    {
        return $this->location;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function getEpisodes()
    {
        return $this->episodes;
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

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function setSpecies($species)
    {
        $this->species = $species;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function setGender($gender)
    {
        $this->gender = $gender;
    }

    public function setOrigin(Location $origin)
    {
        $this->origin = $origin;
    }

    public function setLocation(Location $location)
    {
        $this->location = $location;
    }

    public function setImage($image)
    {
        $this->image = $image;
    }

    public function setEpisodes($episodes)
    {
        $this->episodes = $episodes;
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
