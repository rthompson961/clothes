<?php

namespace App\DataFixtures;

use App\Entity\OrderStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class OrderStatusFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $values = [
            'Placed',
            'Out for Delivery',
            'Received',
            'Cancelled',
            'Returned'
        ];
        foreach ($values as $val) {
            $status = new OrderStatus();
            $status->setName($val);
            $manager->persist($status);
            $this->addReference('status-' . str_replace(' ', '-', strtolower($val)), $status);
        }
        
        $manager->flush();
    }
}
