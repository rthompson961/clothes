<?php

namespace App\DataFixtures;

use App\Entity\Brand;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BrandFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $values = [
            'berghaus' => 'Berghaus',
            'hugo' => 'Hugo Boss',
            'jack' => 'Jack & Jones',
            'next' => 'Next',
            'superdry' => 'Superdry',
            'threadbare' => 'Threadbare',
            'tommy' => 'Tommy Hilfiger'
        ];
        foreach ($values as $key => $val) {
            $brand = new Brand();
            $brand->setName($val);
            $manager->persist($brand);
            $this->addReference('brand-' . $key, $brand);
        }

        $manager->flush();
    }
}
