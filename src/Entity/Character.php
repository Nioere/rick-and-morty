<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\JoinColumn;


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
     * @ORM\JoinColumn(name="origin_id", referencedColumnName="id")
     */
    private $origin;

    /**
     * @ORM\ManyToOne(targetEntity="Location", inversedBy="locationCharacters")
     * @ORM\JoinColumn(name="location_id", referencedColumnName="id")
     */
    private $location;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $image;

    /**
     * @ORM\ManyToMany(targetEntity="Episode", inversedBy="characters")
     * @ORM\JoinTable(name="character_episode")
     */
    private $episodes;

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

    public function getOrigin(): ?Location
    {
        return $this->origin;
    }

    public function getLocation(): ?Location
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

    public function setOrigin(Location $origin): self
    {
        $this->origin = $origin;

        return $this;
    }

    public function setLocation(?Location $location): self
    {
        $this->location = $location;

        if ($location !== null) {
            $location->addResident($this);
        }

        return $this;
    }

    public function setImage($image)
    {
        $this->image = $image;
    }

    public function setEpisodes($episodes)
    {
        $this->episodes = $episodes;
    }

    public function setCreated($created)
    {
        $this->created = $created;
    }

    public function getOriginData()
    {
        return [
            'name' => $this->origin->getName(),
            'url' => 'http://localhost:8080/api/location/' . $this->origin->getId(),
        ];

    }

    public function getLocationData()
    {
            return [
                'name' => $this->location->getName(),
                'url' => 'http://localhost:8080/api/location/' . $this->location->getId(),
            ];
    }

}
