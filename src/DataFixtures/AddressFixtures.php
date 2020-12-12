<?php

namespace App\DataFixtures;

use App\Entity\Address;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class AddressFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $userAddress = new Address();
        $userAddress->setUser($this->getReference('user-test'));
        $userAddress->setAddress1('Street test');
        $userAddress->setAddress2('Town test');
        $userAddress->setCounty('County test');
        $userAddress->setPostcode('AB11 2CD');
        $manager->persist($userAddress);
        $this->addReference('address-test', $userAddress);

        $adminAddress = new Address();
        $adminAddress->setUser($this->getReference('user-admin'));
        $adminAddress->setAddress1('Admin Street');
        $adminAddress->setAddress2('Admin Town');
        $adminAddress->setCounty('Admin County');
        $adminAddress->setPostcode('BC22 3DE');
        $manager->persist($adminAddress);
        $this->addReference('address-admin', $adminAddress);
        
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [UserFixtures::class];
    }
}
