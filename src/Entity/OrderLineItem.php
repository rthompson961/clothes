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
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Order", inversedBy="orderLineItems")
     * @ORM\JoinColumn(nullable=false)
     */
    private $OrderParent;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ProductStockItem", inversedBy="orderLineItems")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ProductStockItem;

    /**
     * @ORM\Column(type="integer")
     */
    private $price;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantity;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderParent(): ?Order
    {
        return $this->OrderParent;
    }

    public function setOrderParent(?Order $OrderParent): self
    {
        $this->OrderParent = $OrderParent;

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
