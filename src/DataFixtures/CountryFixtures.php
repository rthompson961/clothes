<?php

namespace App\DataFixtures;

use App\Entity\Country;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class CountryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $country = new Country();
        $country->setName('United Kingdom');
        $manager->persist($country);
        $this->addReference('country-uk', $country);
        
        $manager->flush();
    }
}
