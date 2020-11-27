<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ShopControllerTest extends WebTestCase
{
    public function testShop(): void
    {
        $client = static::createClient();
        $crawler = $client->request(
            'GET',
            '/shop?page=4&sort=low&brand=4,2,5&&colour=2,5'
        );

        $this->assertSelectorTextSame('h4', '22 Products');
        $this->assertEquals(4, $crawler->filter('div.product')->count());
    }

    public function testSearch(): void
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/');

        $crawler = $client->submitForm('search[submit]', [
            'search[terms]' => 'hooded jacket'
        ]);

        $this->assertRouteSame('shop');
        $this->assertSelectorTextSame('h4', '5 Products');
        $this->assertEquals(5, $crawler->filter('div.product')->count());
    }

    public function testArrayQueryStringsSanitised(): void
    {
        $client = static::createClient();
        $crawler = $client->request(
            'GET',
            '/shop?search[]=hooded%20jacket'
        );

        $this->assertResponseIsSuccessful();
    }
}
