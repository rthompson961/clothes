<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ColourRepository")
 */
class Colour
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=15)
     */
    private $name;

    private $addLink;

    private $removeLink;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAddLink(): ?string
    {
        return $this->addLink;
    }

    public function setAddLink(string $addLink): self
    {
        $this->addLink = $addLink;

        return $this;
    }

    public function getRemoveLink(): ?string
    {
        return $this->removeLink;
    }

    public function setRemoveLink(string $removeLink): self
    {
        $this->removeLink = $removeLink;

        return $this;
    }
}
