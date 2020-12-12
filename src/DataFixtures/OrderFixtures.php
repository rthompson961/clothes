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
        $userOrder = new Order();
        $userOrder->setUser($this->getReference('user-test'));
        $userOrder->setAddress($this->getReference('address-test'));
        $userOrder->setTotal(48094);
        $manager->persist($userOrder);
        $this->addReference('order-user', $userOrder);

        $adminOrder = new Order();
        $adminOrder->setUser($this->getReference('user-admin'));
        $adminOrder->setAddress($this->getReference('address-admin'));
        $adminOrder->setTotal(24297);
        $manager->persist($adminOrder);
        $this->addReference('order-admin', $adminOrder);
        
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
