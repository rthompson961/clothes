<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OrderLineItemRepository")
 */
class OrderLineItem
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Order", inversedBy="orderLineItems")
     * @ORM\JoinColumn(nullable=false)
     */
    private Order $order;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ProductStockItem")
     * @ORM\JoinColumn(nullable=false)
     */
    private ProductStockItem $productStockItem;

    /**
     * @ORM\Column(type="integer")
     */
    private int $price;

    /**
     * @ORM\Column(type="integer")
     */
    private int $quantity;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function setOrder(Order $order): self
    {
        $this->order = $order;

        return $this;
    }

    public function getProductStockItem(): ProductStockItem
    {
        return $this->productStockItem;
    }

    public function setProductStockItem(ProductStockItem $productStockItem): self
    {
        $this->productStockItem = $productStockItem;

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

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }
}
