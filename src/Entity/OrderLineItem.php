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
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\OrderTotal", inversedBy="orderLineItems")
     * @ORM\JoinColumn(nullable=false)
     */
    private OrderTotal $OrderTotal;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ProductStockItem", inversedBy="orderLineItems")
     * @ORM\JoinColumn(nullable=false)
     */
    private ProductStockItem $ProductStockItem;

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

    public function getOrderTotal(): ?OrderTotal
    {
        return $this->OrderTotal;
    }

    public function setOrderTotal(?OrderTotal $OrderTotal): self
    {
        $this->OrderTotal = $OrderTotal;

        return $this;
    }

    public function getProductStockItem(): ?ProductStockItem
    {
        return $this->ProductStockItem;
    }

    public function setProductStockItem(?ProductStockItem $ProductStockItem): self
    {
        $this->ProductStockItem = $ProductStockItem;

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

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }
}
