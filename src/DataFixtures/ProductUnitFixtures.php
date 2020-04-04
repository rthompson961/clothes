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
                $unit = new ProductUnit();
                $unit->setProduct($this->getReference('product-' . $i));
                $unit->setSize($this->getReference('size-' . $size));
                $unit->setStock($stock);
                $manager->persist($unit);

                // used for order line item fixtures
                if ($i === 10 && $size == 'large') {
                    $this->addReference('orderitem-one', $unit);
                }
                if ($i === 34 && $size == 'medium') {
                    $this->addReference('orderitem-two', $unit);
                }
                if ($i === 43 && $size == 'medium') {
                    $this->addReference('orderitem-three', $unit);
                }
                if ($i === 60 && $size == 'small') {
                    $this->addReference('orderitem-four', $unit);
                }
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
