<?php

namespace App\DataFixtures;

use App\Entity\ProductGroup;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class ProductGroupFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $values = [
            1 => 'Next Down Filled Jacket',
            2 => 'Dupont Hooded Quilted Jacket',
            3 => 'Next Shower Resistant Utility Parka',
            4 => 'Next Fleece Zip Neck Pullover',
            5 => 'Next Crew Sweatshirt',
            6 => 'Hugo Boss Salbo Crew Sweatshirt'
        ];
        foreach ($values as $key => $val) {
            $group = new ProductGroup();
            $group->setName($val);
            $manager->persist($group);
            $this->addReference('group-' . $key, $group);
        }

        $manager->flush();
    }
}
