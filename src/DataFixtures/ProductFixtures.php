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
    public function load(ObjectManager $manager): void
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
            31 => [
                'name' => 'Hugo Boss Authentic Hoodie Black',
                'price' => 7999,
                'category' => $this->getReference('category-hoodies'),
                'brand' => $this->getReference('brand-hugo'),
                'colour' => $this->getReference('colour-black'),
            ],
            32 => [
                'name' => 'Hugo Boss Curved Logo Hoodie Grey',
                'price' => 16999,
                'category' => $this->getReference('category-hoodies'),
                'brand' => $this->getReference('brand-hugo'),
                'colour' => $this->getReference('colour-grey'),
            ],
            33 => [
                'name' => 'Hugo Boss Authentic Logo Hoodie Navy',
                'price' => 7999,
                'category' => $this->getReference('category-hoodies'),
                'brand' => $this->getReference('brand-hugo'),
                'colour' => $this->getReference('colour-navy'),
            ],
            34 => [
                'name' => 'Tommy Hilfiger Embossed Logo Hoodie Red',
                'price' => 10999,
                'category' => $this->getReference('category-hoodies'),
                'brand' => $this->getReference('brand-tommy'),
                'colour' => $this->getReference('colour-red'),
            ],
            35 => [
                'name' => 'Tommy Hilfiger Grey Flag Hoodie Grey',
                'price' => 7999,
                'category' => $this->getReference('category-hoodies'),
                'brand' => $this->getReference('brand-tommy'),
                'colour' => $this->getReference('colour-grey'),
            ],
            36 => [
                'name' => 'Tommy Hilfiger Sport Logo Zip Hoodie Black',
                'price' => 7999,
                'category' => $this->getReference('category-hoodies'),
                'brand' => $this->getReference('brand-tommy'),
                'colour' => $this->getReference('colour-black'),
            ],
            37 => [
                'name' => 'Threadbare Sweat Hoodie Black',
                'price' => 1999,
                'category' => $this->getReference('category-hoodies'),
                'brand' => $this->getReference('brand-threadbare'),
                'colour' => $this->getReference('colour-black'),
            ],
            38 => [
                'name' => 'Threadbare Sweat Hoodie Grey',
                'price' => 1799,
                'category' => $this->getReference('category-hoodies'),
                'brand' => $this->getReference('brand-threadbare'),
                'colour' => $this->getReference('colour-grey'),
            ],
            39 => [
                'name' => 'Threadbare Sweat Hoodie Navy',
                'price' => 2999,
                'category' => $this->getReference('category-hoodies'),
                'brand' => $this->getReference('brand-threadbare'),
                'colour' => $this->getReference('colour-navy'),
            ],
            40 => [
                'name' => 'Jack & Jones Retro Hoodie Navy',
                'price' => 2999,
                'category' => $this->getReference('category-hoodies'),
                'brand' => $this->getReference('brand-jack'),
                'colour' => $this->getReference('colour-navy'),
            ],
            41 => [
                'name' => 'Superdry Urban Varsity T-Shirt White',
                'price' => 2499,
                'category' => $this->getReference('category-t-shirts'),
                'brand' => $this->getReference('brand-superdry'),
                'colour' => $this->getReference('colour-white'),
            ],
            42 => [
                'name' => 'Superdry Merch Store Patch T-Shirt Blue',
                'price' => 2399,
                'category' => $this->getReference('category-t-shirts'),
                'brand' => $this->getReference('brand-superdry'),
                'colour' => $this->getReference('colour-blue'),
            ],
            43 => [
                'name' => 'Superdry City Neon Oversized T-Shirt Orange',
                'price' => 2299,
                'category' => $this->getReference('category-t-shirts'),
                'brand' => $this->getReference('brand-superdry'),
                'colour' => $this->getReference('colour-orange'),
            ],
            44 => [
                'name' => 'Superdry Hoops T-Shirt Red',
                'price' => 2499,
                'category' => $this->getReference('category-t-shirts'),
                'brand' => $this->getReference('brand-superdry'),
                'colour' => $this->getReference('colour-red'),
            ],
            45 => [
                'name' => 'Superdry Classic Varsity T-Shirt Blue',
                'price' => 2499,
                'category' => $this->getReference('category-t-shirts'),
                'brand' => $this->getReference('brand-superdry'),
                'colour' => $this->getReference('colour-blue'),
            ],
            46 => [
                'name' => 'Superdry Vintage Embroidery T-Shirt Plum',
                'price' => 2499,
                'category' => $this->getReference('category-t-shirts'),
                'brand' => $this->getReference('brand-superdry'),
                'colour' => $this->getReference('colour-plum'),
            ],
            47 => [
                'name' => 'Hugo Boss Tales T-Shirt Navy',
                'price' => 4499,
                'category' => $this->getReference('category-t-shirts'),
                'brand' => $this->getReference('brand-hugo'),
                'colour' => $this->getReference('colour-navy'),
            ],
            48 => [
                'name' => 'Hugo Boss Logo T-Shirt White',
                'price' => 3499,
                'category' => $this->getReference('category-t-shirts'),
                'brand' => $this->getReference('brand-hugo'),
                'colour' => $this->getReference('colour-white'),
            ],
            49 => [
                'name' => 'Hugo Boss Logo T-Shirt Navy',
                'price' => 3899,
                'category' => $this->getReference('category-t-shirts'),
                'brand' => $this->getReference('brand-hugo'),
                'colour' => $this->getReference('colour-navy'),
            ],
            50 => [
                'name' => 'Tommy Hilfiger Tape Logo T-Shirt Blue',
                'price' => 3899,
                'category' => $this->getReference('category-t-shirts'),
                'brand' => $this->getReference('brand-tommy'),
                'colour' => $this->getReference('colour-blue'),
            ],
            51 => [
                'name' => 'Tommy Hilfiger Block Stripe T-Shirt Navy',
                'price' => 3999,
                'category' => $this->getReference('category-t-shirts'),
                'brand' => $this->getReference('brand-tommy'),
                'colour' => $this->getReference('colour-navy'),
            ],
            52 => [
                'name' => 'Tommy Hilfiger Logo T-Shirt White',
                'price' => 3499,
                'category' => $this->getReference('category-t-shirts'),
                'brand' => $this->getReference('brand-tommy'),
                'colour' => $this->getReference('colour-white'),
            ],
            53 => [
                'name' => 'Hugo Boss Tales Square Logo T-Shirt Red',
                'price' => 3499,
                'category' => $this->getReference('category-t-shirts'),
                'brand' => $this->getReference('brand-hugo'),
                'colour' => $this->getReference('colour-red'),
            ],
            54 => [
                'name' => 'Tommy Hilfiger Cotton Icon T-Shirt Black',
                'price' => 3499,
                'category' => $this->getReference('category-t-shirts'),
                'brand' => $this->getReference('brand-tommy'),
                'colour' => $this->getReference('colour-black'),
            ],
            55 => [
                'name' => 'Superdry Tyler Slim Flex Jeans Black',
                'price' => 6999,
                'category' => $this->getReference('category-jeans'),
                'brand' => $this->getReference('brand-superdry'),
                'colour' => $this->getReference('colour-black'),
            ],
            56 => [
                'name' => 'Superdry Daman Straight Jeans Navy',
                'price' => 6499,
                'category' => $this->getReference('category-jeans'),
                'brand' => $this->getReference('brand-superdry'),
                'colour' => $this->getReference('colour-navy'),
            ],
            57 => [
                'name' => 'Superdry Daman Straight Jeans Black',
                'price' => 6299,
                'category' => $this->getReference('category-jeans'),
                'brand' => $this->getReference('brand-superdry'),
                'colour' => $this->getReference('colour-black'),
            ],
            58 => [
                'name' => 'Superdry Tyler Slim Flex Jeans Grey',
                'price' => 6999,
                'category' => $this->getReference('category-jeans'),
                'brand' => $this->getReference('brand-superdry'),
                'colour' => $this->getReference('colour-grey'),
            ],
            59 => [
                'name' => 'Hugo Boss Maine Jeans Black',
                'price' => 10899,
                'category' => $this->getReference('category-jeans'),
                'brand' => $this->getReference('brand-hugo'),
                'colour' => $this->getReference('colour-black'),
            ],
            60 => [
                'name' => 'Tommy Hilfiger Layton Slim Jeans Blue',
                'price' => 10999,
                'category' => $this->getReference('category-jeans'),
                'brand' => $this->getReference('brand-tommy'),
                'colour' => $this->getReference('colour-blue'),
            ],
            61 => [
                'name' => 'Hugo Boss 708 Jeans Navy',
                'price' => 10499,
                'category' => $this->getReference('category-jeans'),
                'brand' => $this->getReference('brand-hugo'),
                'colour' => $this->getReference('colour-navy'),
            ],
            62 => [
                'name' => 'Tommy Hilfiger Black Core Denton Jeans Black',
                'price' => 8999,
                'category' => $this->getReference('category-jeans'),
                'brand' => $this->getReference('brand-tommy'),
                'colour' => $this->getReference('colour-black'),
            ],
            63 => [
                'name' => 'Hugo Boss Straight Fit Jeans Navy',
                'price' => 8899,
                'category' => $this->getReference('category-jeans'),
                'brand' => $this->getReference('brand-hugo'),
                'colour' => $this->getReference('colour-navy'),
            ],
            64 => [
                'name' => 'Hugo Boss Maine Jeans Navy',
                'price' => 10899,
                'category' => $this->getReference('category-jeans'),
                'brand' => $this->getReference('brand-hugo'),
                'colour' => $this->getReference('colour-navy'),
            ],
            65 => [
                'name' => 'Hugo Boss Charleston Jeans Grey',
                'price' => 12899,
                'category' => $this->getReference('category-jeans'),
                'brand' => $this->getReference('brand-hugo'),
                'colour' => $this->getReference('colour-grey'),
            ],
            66 => [
                'name' => 'Threadbare Ripped Jeans Black',
                'price' => 1999,
                'category' => $this->getReference('category-jeans'),
                'brand' => $this->getReference('brand-threadbare'),
                'colour' => $this->getReference('colour-black'),
            ]
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

    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class,
            BrandFixtures::class,
            ColourFixtures::class,
            ProductGroupFixtures::class
        ];
    }
}
