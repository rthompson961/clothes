<?php

namespace App\DataFixtures;

use App\Entity\OrderTotal;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class OrderTotalFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $order = new OrderTotal();
        $order->setUser($this->getReference('user-test'));
        $order->setAddress($this->getReference('address-test'));
        $order->setStatus($this->getReference('status-received'));
        $order->setTotal(48094);
        $manager->persist($order);
        $this->addReference('order-test', $order);
        
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            AddressFixtures::class,
            OrderStatusFixtures::class
        ];
    }
}
