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
    public function load(ObjectManager $manager): void
    {
        $productCount = 66;
        $maxStock = 30;

        for ($i = 1; $i <= $productCount; $i++) {
            foreach (['small', 'medium', 'large'] as $size) {
                if ($i === 1 && $size == 'small' || $i === 1 && $size == 'medium') {
                    // in stock items needed for testing
                    $stock = 20;
                } elseif ($i === 1 && $size == 'large') {
                    // out of stock item needed for testing
                    $stock = 0;
                } elseif (mt_rand(1, 5) == 5) {
                    $stock = 0;
                } else {
                    $stock = mt_rand(1, $maxStock);
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

    public function getDependencies(): array
    {
        return [
            ProductFixtures::class,
            SizeFixtures::class,
        ];
    }
}
