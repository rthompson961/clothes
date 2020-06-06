<?php

namespace App\DataFixtures;

use App\Entity\ProductUnit;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ProductUnitFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $products = 66;
        $unit = 0;
        $maxStock = 30;
        for ($i = 1; $i <= $products; $i++) {
            $product = $this->getReference('product-' . $i);
            foreach (['small', 'medium', 'large'] as $size) {
                $unit++;
                // randomise stock with 1 in 5 being out of stock
                if (mt_rand(1, 5) == 5) {
                    $stock = 0;
                } else {
                    $stock = mt_rand(1, $maxStock);
                }
                // in stock units needed for testing
                if ($unit == 1 || $unit == 2) {
                    $stock = 20;
                }
                // out of stock item needed for testing
                if ($unit == 3) {
                    $stock = 0;
                }

                $productUnit = new ProductUnit();
                $productUnit->setProduct($product);
                $productUnit->setSize($this->getReference('size-' . $size));
                $productUnit->setPrice($product->getPrice());
                $productUnit->setStock($stock);
                $manager->persist($productUnit);

                $this->addReference('unit-' . $unit, $productUnit);
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
