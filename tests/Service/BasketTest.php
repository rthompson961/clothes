<?php

namespace App\Tests\Service;

use App\Entity\ProductUnit;
use App\Service\Basket;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BasketTest extends KernelTestCase
{
    private Basket $basket;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $em = $kernel->getContainer()->get('doctrine')->getManager();
        $repo = $em->getRepository(ProductUnit::class);

        $this->basket = new Basket($repo);
    }

    public function testProducts(): array
    {
        $items = [128 => 3, 179 => 1, 102 => 4];
        $products = $this->basket->getProducts($items);
     
        $this->assertTrue($products[0]['name']  == 'Superdry City Neon Oversized T-Shirt Orange');
        $this->assertTrue($products[0]['size']  == 'Medium');
        $this->assertTrue($products[0]['price'] == 2299);

        $this->assertTrue($products[1]['name']  == 'Tommy Hilfiger Layton Slim Jeans Blue');
        $this->assertTrue($products[1]['size']  == 'Medium');
        $this->assertTrue($products[1]['price'] == 10999);

        $this->assertTrue($products[2]['name']  == 'Tommy Hilfiger Embossed Logo Hoodie Red');
        $this->assertTrue($products[2]['size']  == 'Large');
        $this->assertTrue($products[2]['price'] == 10999);

        return $products;
    }

    /**
     * @depends testProducts
     */
    public function testTotal(array $products): void
    {
        $total = $this->basket->getTotal($products);

        $this->assertTrue($total == 61892);
    }
}
