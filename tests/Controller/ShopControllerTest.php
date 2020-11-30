<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ShopControllerTest extends WebTestCase
{
    public function testArrayQueryStringsSanitised(): void
    {
        $client = static::createClient();
        $crawler = $client->request(
            'GET',
            '/shop?search[]=hooded%20jacket'
        );

        $this->assertResponseIsSuccessful();
    }

    public function testSearchForm(): void
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

    public function testFilters(): void
    {
        $client = static::createClient();
        $crawler = $client->request(
            'GET',
            '/shop?search=next%20sweat&colour=4,5,9'
        );

        $this->assertSelectorTextSame('h4', '4 Products');
        $this->assertEquals(4, $crawler->filter('div.product')->count());
    }

    public function testSort(): void
    {
        $client = static::createClient();
        $crawler = $client->request(
            'GET',
            '/shop?sort=name&category=1,4,6&colour=2,5,10'
        );

        $this->assertSelectorTextSame('div.product p', 'Hugo Boss Authentic Sweatshirt Black');
    }

    public function testPage(): void
    {
        $client = static::createClient();
        $crawler = $client->request(
            'GET',
            '/shop?page=4&sort=name&category=1,4,6&colour=2,5,10'
        );

        $this->assertSelectorTextSame('h4', '19 Products');
        $this->assertEquals(1, $crawler->filter('div.product')->count());
    }
}
