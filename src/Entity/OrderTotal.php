<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OrderTotalRepository")
 */
class OrderTotal
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
    private User $User;

    /**
     * @ORM\Column(type="integer")
     */
    private int $total;

    /**
     * @var Collection<OrderLineItem>
     * @ORM\OneToMany(targetEntity="App\Entity\OrderLineItem", mappedBy="OrderTotal")
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

    public function getUser(): ?User
    {
        return $this->User;
    }

    public function setUser(?User $User): self
    {
        $this->User = $User;

        return $this;
    }

    public function getTotal(): ?int
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

    public function addOrderLineItem(OrderLineItem $orderLineItem): self
    {
        if (!$this->orderLineItems->contains($orderLineItem)) {
            $this->orderLineItems[] = $orderLineItem;
            $orderLineItem->setOrderTotal($this);
        }

        return $this;
    }

    public function removeOrderLineItem(OrderLineItem $orderLineItem): self
    {
        if ($this->orderLineItems->contains($orderLineItem)) {
            $this->orderLineItems->removeElement($orderLineItem);
            // set the owning side to null (unless already changed)
            if ($orderLineItem->getOrderTotal() === $this) {
                $orderLineItem->setOrderTotal(null);
            }
        }

        return $this;
    }
}
