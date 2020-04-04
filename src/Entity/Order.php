<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OrderRepository")
 * @ORM\Table(name="`order`")
 */
class Order
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="orders")
     * @ORM\JoinColumn(nullable=false)
     */
    private User $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Address")
     * @ORM\JoinColumn(nullable=false)
     */
    private Address $address;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\OrderStatus")
     * @ORM\JoinColumn(nullable=false)
     */
    private OrderStatus $status;

    /**
     * @ORM\Column(type="integer")
     */
    private int $total;

    /**
     * @var Collection<OrderLineItem>
     * @ORM\OneToMany(targetEntity="App\Entity\OrderLineItem", mappedBy="order")
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

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getAddress(): Address
    {
        return $this->address;
    }

    public function setAddress(Address $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getStatus(): OrderStatus
    {
        return $this->status;
    }

    public function setStatus(OrderStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function setTotal(int $total): self
    {
        $this->total = $total;

        return $this;
    }

    /**
     * @return Collection|OrderLineItem[]
     */
    public function getOrderLineItems(): Collection
    {
        return $this->orderLineItems;
    }
}
