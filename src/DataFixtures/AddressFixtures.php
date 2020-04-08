<?php

namespace App\DataFixtures;

use App\Entity\Address;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class AddressFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $address = new Address();
        $address->setUser($this->getReference('user-test'));
        $address->setAddress1('Street test');
        $address->setAddress2('Town test');
        $address->setCounty('County test');
        $address->setPostcode('AB11 2CD');
        $manager->persist($address);
        $this->addReference('address-test', $address);
        
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [UserFixtures::class];
    }
}
