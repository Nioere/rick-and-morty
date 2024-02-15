<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

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
     * @ORM\ManyToMany(targetEntity="Character", mappedBy="origin")
     */
    private $originCharacters;

    /**
     * @ORM\ManyToMany(targetEntity="Character", mappedBy="location")
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
}
