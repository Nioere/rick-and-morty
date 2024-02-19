<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity
 */
class Location
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $dimension;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Character", mappedBy="location")
     */
    private $residents;

    /**
     * @ORM\OneToMany(targetEntity="Character", mappedBy="origin")
     */
    private $originCharacters;

    /**
     * @ORM\OneToMany(targetEntity="Character", mappedBy="location")
     */
    private $locationCharacters;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;

    public function __construct()
    {
        $this->originCharacters = new ArrayCollection();
        $this->locationCharacters = new ArrayCollection();
        $this->residents = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getDimension()
    {
        return $this->dimension;
    }

    public function getOriginCharacters()
    {
        return $this->originCharacters;
    }

    public function getLocationCharacters()
    {
        return $this->locationCharacters;
    }

    public function getCreated()
    {
        return $this->created;
    }


    public function setName($name)
    {
        $this->name = $name;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function setDimension($dimension)
    {
        $this->dimension = $dimension;
    }

    public function setCreated($created)
    {
        $this->created = $created;
    }

    public function addResident(Character $character): self
    {
        if (!$this->residents->contains($character)) {
            $this->residents[] = $character;
            $character->setLocation($this);
        }

        return $this;
    }

    public function removeResident(Character $character): self
    {
        if ($this->residents->contains($character)) {
            $this->residents->removeElement($character);
            if ($character->getLocation() === $this) {
                $character->setLocation(null);
            }
        }

        return $this;
    }

    public function getResidents(): Collection
    {
        return $this->residents;
    }


}
