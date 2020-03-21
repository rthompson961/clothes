<?php

namespace App\DataFixtures;

use App\Entity\OrderLineitem;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class OrderLineItemFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $values = [
            [
                'stock' => $this->getReference('orderitem-one'),
                'price' => 10499,
                'quantity' => 1
            ],
            [
                'stock' => $this->getReference('orderitem-two'),
                'price' => 10999,
                'quantity' => 1
            ],
            [
                'stock' => $this->getReference('orderitem-three'),
                'price' => 2299,
                'quantity' => 2
            ],
            [
                'stock' => $this->getReference('orderitem-four'),
                'price' => 10999,
                'quantity' => 2
            ]
        ];

        foreach ($values as $val) {
            $item = new OrderLineItem();
            $item->setOrderTotal($this->getReference('order-test'));
            $item->setProductStockItem($val['stock']);
            $item->setPrice($val['price']);
            $item->setQuantity($val['quantity']);
            $manager->persist($item);
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            OrderTotalFixtures::class,
            ProductStockItemFixtures::class
        ];
    }
}
