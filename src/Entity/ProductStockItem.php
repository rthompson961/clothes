<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductStockItemRepository")
 */
class ProductStockItem
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Product", inversedBy="productStockItems")
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

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\OrderLineItem", mappedBy="ProductStockItem")
     */
    private Collection $orderLineItems;

    public function __construct()
    {
        $this->orderLineItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getSize(): ?Size
    {
        return $this->size;
    }

    public function setSize(?Size $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): self
    {
        $this->stock = $stock;

        return $this;
    }

    /**
     * @return Collection|OrderLineItem[]
     */
    public function getOrderLineItems(): Collection
    {
        return $this->orderLineItems;
    }

    public function addOrderLineItem(OrderLineItem $orderLineItem): self
    {
        if (!$this->orderLineItems->contains($orderLineItem)) {
            $this->orderLineItems[] = $orderLineItem;
            $orderLineItem->setProductStockItem($this);
        }

        return $this;
    }

    public function removeOrderLineItem(OrderLineItem $orderLineItem): self
    {
        if ($this->orderLineItems->contains($orderLineItem)) {
            $this->orderLineItems->removeElement($orderLineItem);
            // set the owning side to null (unless already changed)
            if ($orderLineItem->getProductStockItem() === $this) {
                $orderLineItem->setProductStockItem(null);
            }
        }

        return $this;
    }
}
