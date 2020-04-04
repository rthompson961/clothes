<?php

namespace App\DataFixtures;

use App\Entity\OrderItem;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class OrderItemFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $values = [
            [
                'unit' => $this->getReference('orderitem-one'),
                'price' => 10499,
                'quantity' => 1
            ],
            [
                'unit' => $this->getReference('orderitem-two'),
                'price' => 10999,
                'quantity' => 1
            ],
            [
                'unit' => $this->getReference('orderitem-three'),
                'price' => 2299,
                'quantity' => 2
            ],
            [
                'unit' => $this->getReference('orderitem-four'),
                'price' => 10999,
                'quantity' => 2
            ]
        ];

        foreach ($values as $val) {
            $item = new OrderItem();
            $item->setOrder($this->getReference('order-test'));
            $item->setProductUnit($val['unit']);
            $item->setPrice($val['price']);
            $item->setQuantity($val['quantity']);
            $manager->persist($item);
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            OrderFixtures::class,
            ProductUnitFixtures::class
        ];
    }
}
