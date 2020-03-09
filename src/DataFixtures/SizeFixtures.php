<?php

namespace App\DataFixtures;

use App\Entity\Size;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class SizeFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $values = ['Small', 'Medium', 'Large'];
        foreach ($values as $val) {
            $size = new Size();
            $size->setName($val);
            $manager->persist($size);
            $this->addReference('size-' . strtolower($val), $size);
        }
        
        $manager->flush();
    }
}
