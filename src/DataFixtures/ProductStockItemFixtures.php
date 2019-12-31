<?php

namespace App\DataFixtures;

use App\Entity\ProductStockItem;
use App\DataFixtures\ProductFixtures;
use App\DataFixtures\SizeFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ProductStockItemFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i <= 30; $i++) {
            foreach (['small', 'medium', 'large'] as $size) {
                if (mt_rand(1, 5) == 5) {
                    $stock = 0;
                } else {
                    $stock = mt_rand(1, 30);
                }
                $item = new ProductStockItem();
                $item->setProduct($this->getReference('product-' . $i));
                $item->setSize($this->getReference('size-' . $size));
                $item->setStock($stock);
                $manager->persist($item);          
            }
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            ProductFixtures::class,
            SizeFixtures::class,
        ];
    }
}
