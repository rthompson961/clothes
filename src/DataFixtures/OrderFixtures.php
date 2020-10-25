<?php

namespace App\DataFixtures;

use App\Entity\Order;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class OrderFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $order = new Order();
        $order->setUser($this->getReference('user-test'));
        $order->setAddress($this->getReference('address-test'));
        $order->setTotal(48094);
        $manager->persist($order);
        $this->addReference('order-test', $order);
        
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            AddressFixtures::class
        ];
    }
}
