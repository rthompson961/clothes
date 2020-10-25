<?php

namespace App\DataFixtures;

use App\Entity\Colour;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ColourFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $values = [
            'Blue',
            'Black',
            'Olive',
            'Grey',
            'Navy',
            'Orange',
            'Plum',
            'Red',
            'Stone',
            'White'
        ];
        foreach ($values as $val) {
            $colour = new Colour();
            $colour->setName($val);
            $manager->persist($colour);
            $this->addReference('colour-' . strtolower($val), $colour);
        }

        $manager->flush();
    }
}
