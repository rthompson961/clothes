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
    private $id;

    /**
     * @ORM\Column(type="string", length=45)
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     */
    private $price;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Brand")
     * @ORM\JoinColumn(nullable=false)
     */
    private $brand;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Colour")
     * @ORM\JoinColumn(nullable=false)
     */
    private $colour;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ProductGroup", inversedBy="products")
     * @ORM\JoinColumn(nullable=true)
     */
    private $productGroup;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProductStockItem", mappedBy="product")
     */
    private $productStockItems;

    public function __construct()
    {
        $this->productStockItems = new ArrayCollection();
    }

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

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getBrand(): ?Brand
    {
        return $this->brand;
    }

    public function setBrand(?Brand $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getColour(): ?Colour
    {
        return $this->colour;
    }

    public function setColour(?Colour $colour): self
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
     * @return Collection|ProductStockItem[]
     */
    public function getProductStockItems(): Collection
    {
        return $this->productStockItems;
    }

    public function addProductStockItem(ProductStockItem $productStockItem): self
    {
        if (!$this->productStockItems->contains($productStockItem)) {
            $this->productStockItems[] = $productStockItem;
            $productStockItem->setProduct($this);
        }

        return $this;
    }

    public function removeProductStockItem(ProductStockItem $productStockItem): self
    {
        if ($this->productStockItems->contains($productStockItem)) {
            $this->productStockItems->removeElement($productStockItem);
            // set the owning side to null (unless already changed)
            if ($productStockItem->getProduct() === $this) {
                $productStockItem->setProduct(null);
            }
        }

        return $this;
    }
}
