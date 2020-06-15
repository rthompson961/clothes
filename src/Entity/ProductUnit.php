<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductUnitRepository")
 */
class ProductUnit
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Product", inversedBy="productUnits")
     * @ORM\JoinColumn(nullable=false)
     */
    private Product $product;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Size")
     * @ORM\JoinColumn(nullable=false)
     */
    private Size $size;

    /**
     * @ORM\Column(type="integer")
     */
    private int $stock;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getSize(): Size
    {
        return $this->size;
    }

    public function setSize(Size $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getStock(): int
    {
        return $this->stock;
    }

    public function setStock(int $stock): self
    {
        $this->stock = $stock;

        return $this;
    }
}
