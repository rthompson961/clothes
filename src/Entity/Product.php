<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 */
class Product
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=45)
     */
    private string $name;

    /**
     * @ORM\Column(type="integer")
     */
    private int $price;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category")
     * @ORM\JoinColumn(nullable=false)
     */
    private Category $category;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Brand")
     * @ORM\JoinColumn(nullable=false)
     */
    private Brand $brand;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Colour", fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
     */
    private Colour $colour;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ProductGroup", inversedBy="products", fetch="EAGER")
     * @ORM\JoinColumn(nullable=true)
     */
    private ?ProductGroup $productGroup = null;

    /**
     * @var Collection<ProductUnit>
     * @ORM\OneToMany(targetEntity="App\Entity\ProductUnit", mappedBy="product")
     */
    private Collection $productUnits;

    public function __construct()
    {
        $this->productUnits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getCategory(): Category
    {
        return $this->category;
    }

    public function setCategory(Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getBrand(): Brand
    {
        return $this->brand;
    }

    public function setBrand(Brand $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getColour(): Colour
    {
        return $this->colour;
    }

    public function setColour(Colour $colour): self
    {
        $this->colour = $colour;

        return $this;
    }

    public function getProductGroup(): ?ProductGroup
    {
        return $this->productGroup;
    }

    public function setProductGroup(?ProductGroup $productGroup): self
    {
        $this->productGroup = $productGroup;

        return $this;
    }

    /**
     * @return Collection|ProductUnit[]
     */
    public function getProductUnits(): Collection
    {
        return $this->productUnits;
    }
}
