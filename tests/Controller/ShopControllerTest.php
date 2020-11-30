<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ShopControllerTest extends WebTestCase
{
    public function testArrayQueryStringsSanitised(): void
    {
        $client = static::createClient();
        $client->request(
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

    public function testSearch(): void
    {
        $client = static::createClient();
        $crawler = $client->request(
            'GET',
            '/shop?search=next%20sweat&colour=4,5,9'
        );

        $this->assertSelectorTextSame('h4', '4 Products');
        $this->assertEquals(4, $crawler->filter('div.product')->count());
    }

    public function testFilters(): void
    {
        $client = static::createClient();
        $crawler = $client->request(
            'GET',
            '/shop?page=4&sort=first&category=1,4,6&brand=4,5'
        );

        $this->assertSelectorTextSame('h4', '21 Products');
        $this->assertEquals(3, $crawler->filter('div.product')->count());
    }

    /**
     * @dataProvider sortProvider
     */
    public function testSort(string $sort, string $product): void
    {
        $client = static::createClient();
        $client->request('GET', '/shop?sort=' . $sort);

        $this->assertSelectorTextSame('div.product p', $product);
    }

    public function sortProvider(): array
    {
        return [
            ['', 'Next Down Filled Jacket Olive'],
            ['first', 'Next Down Filled Jacket Olive'],
            ['name', 'Berghaus Syker Sherpa Fleece White'],
            ['low', 'Threadbare Sweat Hoodie Grey'],
            ['high', 'Hugo Boss Curved Logo Hoodie Grey']
        ];
    }

    public function testPage(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/shop?page=11');

        $this->assertEquals(6, $crawler->filter('div.product')->count());
    }
}
