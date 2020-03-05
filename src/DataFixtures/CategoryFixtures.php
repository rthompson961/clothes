<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $values = [
            'Fleeces',
            'Jackets',
            'Parkas',
            'Sweatshirts',
            'Hoodies',
            'T-Shirts',
            'Jeans'
        ];
        foreach ($values as $val) {
            $category = new Category();
            $category->setName($val);
            $manager->persist($category);
            $this->addReference('category-' . strtolower($val), $category);
        }
        $manager->flush();
    }
}
