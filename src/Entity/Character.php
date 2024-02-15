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
     * @ORM\ManyToMany(targetEntity="Location")
     * @ORM\JoinTable(name="character_origin",
     *      joinColumns={@ORM\JoinColumn(name="character_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="location_id", referencedColumnName="id")}
     * )
     */
    private $origin;

    /**
     * @ORM\ManyToMany(targetEntity="Location")
     * @ORM\JoinTable(name="character_location",
     *      joinColumns={@ORM\JoinColumn(name="character_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="location_id", referencedColumnName="id")}
     * )
     */
    private $location;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $image;

    /**
     * @ORM\ManyToMany(targetEntity="Episode", inversedBy="characters")
     * @ORM\JoinTable(name="characters_episode")
     */
    private $episodes;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;

    public function __construct()
    {
        $this->episodes = new ArrayCollection();
        $this->origin = new ArrayCollection();
        $this->location = new ArrayCollection();
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

    public function setCreated($created)
    {
        $this->created = $created;
    }


    public function addOrigin(Location $origin)
    {
        if (!$this->origin->contains($origin)) {
            $this->origin[] = $origin;
        }
    }

    public function removeOrigin(Location $origin)
    {
        $this->origin->removeElement($origin);
    }

    public function addLocation(Location $location)
    {
        if (!$this->location->contains($location)) {
            $this->location[] = $location;
        }
    }

    public function removeLocation(Location $location)
    {
        $this->location->removeElement($location);
    }

    public function getOriginArray()
    {
        return $this->origin->map(function (Location $location) {
            return [
                'name' => $location->getName(),
                'url' => 'http://localhost:8080/api/location/' . $location->getId(),
            ];
        })->toArray();
    }

    public function getLocationArray()
    {
        return $this->location->map(function (Location $location) {
            return [
                'name' => $location->getName(),
                'url' => 'http://localhost:8080/api/location/' . $location->getId(),
            ];
        })->toArray();
    }


}
