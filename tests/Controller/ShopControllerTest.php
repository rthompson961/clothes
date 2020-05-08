<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ShopControllerTest extends WebTestCase
{
    public function testShop(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/shop?page=4&sort=low&brand=4,2,5&colour=2,5');

        $this->assertSelectorTextSame('h4', '22 Products');
        $this->assertEquals(4, $crawler->filter('div.product')->count());
    }
}
