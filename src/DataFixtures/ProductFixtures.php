<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\DataFixtures\CategoryFixtures;
use App\DataFixtures\BrandFixtures;
use App\DataFixtures\ColourFixtures;
use App\DataFixtures\ProductGroupFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ProductFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $values = [
            1 => [
                'name' => 'Next Down Filled Jacket Olive',
                'price' => 4999,
                'category' => $this->getReference('category-jackets'),
                'brand' => $this->getReference('brand-next'),
                'colour' => $this->getReference('colour-olive'),
                'productGroup' => $this->getReference('group-1')
            ],
            2 => [
                'name' => 'Next Down Filled Jacket Navy',
                'price' => 4999,
                'category' => $this->getReference('category-jackets'),
                'brand' => $this->getReference('brand-next'),
                'colour' => $this->getReference('colour-navy'),
                'productGroup' => $this->getReference('group-1')
            ],
            3 => [
                'name' => 'Next Hooded Quilted Jacket Orange',
                'price' => 5999,
                'category' => $this->getReference('category-jackets'),
                'brand' => $this->getReference('brand-next'),
                'colour' => $this->getReference('colour-orange'),
                'productGroup' => $this->getReference('group-2')
            ],
            4 => [
                'name' => 'Next Hooded Quilted Jacket Black',
                'price' => 5999,
                'category' => $this->getReference('category-jackets'),
                'brand' => $this->getReference('brand-next'),
                'colour' => $this->getReference('colour-black'),
                'productGroup' => $this->getReference('group-2')
            ],
            5 => [
                'name' => 'Next Hooded Quilted Jacket Olive',
                'price' => 5999,
                'category' => $this->getReference('category-jackets'),
                'brand' => $this->getReference('brand-next'),
                'colour' => $this->getReference('colour-olive'),
                'productGroup' => $this->getReference('group-2')
            ],
            6 => [
                'name' => 'Next Hooded Quilted Jacket Navy',
                'price' => 5999,
                'category' => $this->getReference('category-jackets'),
                'brand' => $this->getReference('brand-next'),
                'colour' => $this->getReference('colour-navy'),
                'productGroup' => $this->getReference('group-2')
            ],
            7 => [
                'name' => 'Threadbare Hooded Jacket Black',
                'price' => 8999,
                'category' => $this->getReference('category-jackets'),
                'brand' => $this->getReference('brand-threadbare'),
                'colour' => $this->getReference('colour-black'),
            ],
            8 => [
                'name' => 'Jack & Jones Padded Jacket Navy',
                'price' => 7999,
                'category' => $this->getReference('category-jackets'),
                'brand' => $this->getReference('brand-jack'),
                'colour' => $this->getReference('colour-navy'),
            ],
            9 => [
                'name' => 'Jack & Jones Originals Jacket Plum',
                'price' => 7999,
                'category' => $this->getReference('category-jackets'),
                'brand' => $this->getReference('brand-jack'),
                'colour' => $this->getReference('colour-plum'),
            ],
            10 => [
                'name' => 'Superdry Fuji Triple Zip Jacket Blue',
                'price' => 10499,
                'category' => $this->getReference('category-jackets'),
                'brand' => $this->getReference('brand-superdry'),
                'colour' => $this->getReference('colour-blue'),
            ],
            11 => [
                'name' => 'Tommy Hilfiger Essential Down Jacket Red',
                'price' => 11999,
                'category' => $this->getReference('category-jackets'),
                'brand' => $this->getReference('brand-tommy'),
                'colour' => $this->getReference('colour-red'),
            ],
            12 => [
                'name' => 'Next Shower Resistant Utility Parka Orange',
                'price' => 7799,
                'category' => $this->getReference('category-parkas'),
                'brand' => $this->getReference('brand-next'),
                'colour' => $this->getReference('colour-orange'),
                'productGroup' => $this->getReference('group-3')
            ],
            13 => [
                'name' => 'Next Shower Resistant Utility Parka Olive',
                'price' => 7799,
                'category' => $this->getReference('category-parkas'),
                'brand' => $this->getReference('brand-next'),
                'colour' => $this->getReference('colour-olive'),
                'productGroup' => $this->getReference('group-3')
            ],
            14 => [
                'name' => 'Next Shower Resistant Utility Parka Stone',
                'price' => 7799,
                'category' => $this->getReference('category-parkas'),
                'brand' => $this->getReference('brand-next'),
                'colour' => $this->getReference('colour-stone'),
                'productGroup' => $this->getReference('group-3')
            ],
            15 => [
                'name' => 'Superdry Arctic Windcheater Jacket Black',
                'price' => 7499,
                'category' => $this->getReference('category-jackets'),
                'brand' => $this->getReference('brand-superdry'),
                'colour' => $this->getReference('colour-black'),
            ],
            16 => [
                'name' => 'Superdry Everest Parka Black',
                'price' => 12999,
                'category' => $this->getReference('category-parkas'),
                'brand' => $this->getReference('brand-superdry'),
                'colour' => $this->getReference('colour-black'),
            ],
            17 => [
                'name' => 'Next Zip Neck Pullover Fleece Black',
                'price' => 2499,
                'category' => $this->getReference('category-fleeces'),
                'brand' => $this->getReference('brand-next'),
                'colour' => $this->getReference('colour-black'),
                'productGroup' => $this->getReference('group-4')
            ],
            18 => [
                'name' => 'Next Zip Neck Pullover Fleece Navy',
                'price' => 2499,
                'category' => $this->getReference('category-fleeces'),
                'brand' => $this->getReference('brand-next'),
                'colour' => $this->getReference('colour-navy'),
                'productGroup' => $this->getReference('group-4')
            ],
            19 => [
                'name' => 'Next Zip Neck Pullover Fleece Olive',
                'price' => 2499,
                'category' => $this->getReference('category-fleeces'),
                'brand' => $this->getReference('brand-next'),
                'colour' => $this->getReference('colour-olive'),
                'productGroup' => $this->getReference('group-4')
            ],
            20 => [
                'name' => 'Berghaus Syker Sherpa Fleece White',
                'price' => 10999,
                'category' => $this->getReference('category-fleeces'),
                'brand' => $this->getReference('brand-berghaus'),
                'colour' => $this->getReference('colour-white'),
            ],
            21 => [
                'name' => 'Next Crew Sweatshirt Grey',
                'price' => 2199,
                'category' => $this->getReference('category-sweatshirts'),
                'brand' => $this->getReference('brand-next'),
                'colour' => $this->getReference('colour-grey'),
                'productGroup' => $this->getReference('group-5')
            ],
            22 => [
                'name' => 'Next Crew Sweatshirt Black',
                'price' => 2199,
                'category' => $this->getReference('category-sweatshirts'),
                'brand' => $this->getReference('brand-next'),
                'colour' => $this->getReference('colour-black'),
                'productGroup' => $this->getReference('group-5')
            ],
            23 => [
                'name' => 'Next Crew Sweatshirt Blue',
                'price' => 2199,
                'category' => $this->getReference('category-sweatshirts'),
                'brand' => $this->getReference('brand-next'),
                'colour' => $this->getReference('colour-blue'),
                'productGroup' => $this->getReference('group-5')
            ],
            24 => [
                'name' => 'Next Crew Sweatshirt Plum',
                'price' => 2199,
                'category' => $this->getReference('category-sweatshirts'),
                'brand' => $this->getReference('brand-next'),
                'colour' => $this->getReference('colour-plum'),
                'productGroup' => $this->getReference('group-5')
            ],
            25 => [
                'name' => 'Next Crew Sweatshirt Red',
                'price' => 2199,
                'category' => $this->getReference('category-sweatshirts'),
                'brand' => $this->getReference('brand-next'),
                'colour' => $this->getReference('colour-red'),
                'productGroup' => $this->getReference('group-5')
            ],
            26 => [
                'name' => 'Next Crew Sweatshirt Navy',
                'price' => 2199,
                'category' => $this->getReference('category-sweatshirts'),
                'brand' => $this->getReference('brand-next'),
                'colour' => $this->getReference('colour-navy'),
                'productGroup' => $this->getReference('group-5')
            ],
            27 => [
                'name' => 'Hugo Boss Salbo Crew Neck Sweatshirt Red',
                'price' => 11999,
                'category' => $this->getReference('category-sweatshirts'),
                'brand' => $this->getReference('brand-hugo'),
                'colour' => $this->getReference('colour-red'),
                'productGroup' => $this->getReference('group-6')
            ],
            28 => [
                'name' => 'Hugo Boss Salbo Crew Neck Sweatshirt Black',
                'price' => 11999,
                'category' => $this->getReference('category-sweatshirts'),
                'brand' => $this->getReference('brand-hugo'),
                'colour' => $this->getReference('colour-black'),
                'productGroup' => $this->getReference('group-6')
            ],
            29 => [
                'name' => 'Superdry Core Sport Sweatshirt Navy',
                'price' => 3999,
                'category' => $this->getReference('category-sweatshirts'),
                'brand' => $this->getReference('brand-superdry'),
                'colour' => $this->getReference('colour-navy'),
            ],
            30 => [
                'name' => 'Tommy Hilfiger Icon Bomber Jacket Stone',
                'price' => 3999,
                'category' => $this->getReference('category-jackets'),
                'brand' => $this->getReference('brand-tommy'),
                'colour' => $this->getReference('colour-stone'),
            ],
        ];
        foreach ($values as $key => $val) {
            $product = new Product();
            $product->setName($val['name']);
            $product->setPrice($val['price']);
            $product->setCategory($val['category']);
            $product->setBrand($val['brand']);
            $product->setColour($val['colour']);
            if (array_key_exists('productGroup', $val)) {
                $product->setProductGroup($val['productGroup']);
            }
            $manager->persist($product);
            $this->addReference('product-' . $key, $product);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            CategoryFixtures::class,
            BrandFixtures::class,
            ColourFixtures::class,
            ProductGroupFixtures::class
        ];
    }
}
