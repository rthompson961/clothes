<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ShopControllerTest extends WebTestCase
{
    private $client;

    protected function setUp()
    {
        $this->client = static::createClient();
    }

    public function testShopHome()
    {
       $crawler = $this->client->request('GET', '/shop');

       $this->assertResponseIsSuccessful();
       $this->assertSelectorTextSame('h4', '66 Products');
       $this->assertEquals(6, $crawler->filter('div.product')->count());

       $links = $crawler->filter('#sidebar a');
       $this->assertEquals('?page=1&sort=first&category[]=1', $links->first()->attr('href'));
       $this->assertEquals('?page=1&sort=first&colour[]=10', $links->last()->attr('href'));

       $links = $crawler->filter('p.sort a');
       $this->assertEquals('?page=1&sort=name', $links->first()->attr('href'));
       $this->assertEquals('?page=1&sort=high', $links->last()->attr('href'));

       $links = $crawler->filter('p.pages a');
       $this->assertEquals('?page=2&sort=first', $links->first()->attr('href'));
       $this->assertEquals('?page=11&sort=first', $links->last()->attr('href'));
    }

    public function testShopWithPages()
    {
       $crawler = $this->client->request('GET', '/shop?page=5&sort=first');

       $this->assertResponseIsSuccessful();
       $this->assertSelectorTextSame('h4', '66 Products');
       $this->assertEquals(6, $crawler->filter('div.product')->count());

       $links = $crawler->filter('#sidebar a');
       $this->assertEquals('?page=5&sort=first&category[]=1', $links->first()->attr('href'));
       $this->assertEquals('?page=5&sort=first&colour[]=10', $links->last()->attr('href'));

       $links = $crawler->filter('p.sort a');
       $this->assertEquals('?page=5&sort=name', $links->first()->attr('href'));
       $this->assertEquals('?page=5&sort=high', $links->last()->attr('href'));

       $links = $crawler->filter('p.pages a');
       $this->assertEquals('?page=1&sort=first', $links->first()->attr('href'));
       $this->assertEquals('?page=11&sort=first', $links->last()->attr('href'));
    }

    public function testShopWithSort()
    {
       $crawler = $this->client->request('GET', '/shop?page=3&sort=low');

       $this->assertResponseIsSuccessful();
       $this->assertSelectorTextSame('h4', '66 Products');
       $this->assertEquals(6, $crawler->filter('div.product')->count());

       $links = $crawler->filter('#sidebar a');
       $this->assertEquals('?page=3&sort=low&category[]=1', $links->first()->attr('href'));
       $this->assertEquals('?page=3&sort=low&colour[]=10', $links->last()->attr('href'));

       $links = $crawler->filter('p.sort a');
       $this->assertEquals('?page=3&sort=first', $links->first()->attr('href'));
       $this->assertEquals('?page=3&sort=high', $links->last()->attr('href'));

       $links = $crawler->filter('p.pages a');
       $this->assertEquals('?page=1&sort=low', $links->first()->attr('href'));
       $this->assertEquals('?page=11&sort=low', $links->last()->attr('href'));
    }

    public function testShopWithCategories()
    {
       $crawler = $this->client->request('GET', '/shop?page=3&sort=first&category[]=4&category[]=1');

       $this->assertResponseIsSuccessful();
       $this->assertSelectorTextSame('h4', '13 Products');
       $this->assertEquals(1, $crawler->filter('div.product')->count());

       $links = $crawler->filter('#sidebar a');
       $this->assertEquals('?page=3&sort=first&category[]=4', $links->first()->attr('href'));
       $this->assertEquals('?page=3&sort=first&category[]=4&category[]=1&colour[]=10', $links->last()->attr('href'));

       $links = $crawler->filter('p.sort a');
       $this->assertEquals('?page=3&sort=name&category[]=4&category[]=1', $links->first()->attr('href'));
       $this->assertEquals('?page=3&sort=high&category[]=4&category[]=1', $links->last()->attr('href'));

       $links = $crawler->filter('p.pages a');
       $this->assertEquals('?page=1&sort=first&category[]=4&category[]=1', $links->first()->attr('href'));
       $this->assertEquals('?page=2&sort=first&category[]=4&category[]=1', $links->last()->attr('href'));
    }

    public function testShopWithBrands()
    {
       $crawler = $this->client->request('GET', '/shop?page=2&sort=name&brand[]=1&brand[]=6&brand[]=7&brand[]=3&brand[]=2');

       $this->assertResponseIsSuccessful();
       $this->assertSelectorTextSame('h4', '34 Products');
       $this->assertEquals(6, $crawler->filter('div.product')->count());

       $links = $crawler->filter('#sidebar a');
       $this->assertEquals('?page=2&sort=name&brand[]=6&brand[]=7&brand[]=3&brand[]=2', $links->first()->attr('href'));
       $this->assertEquals('?page=2&sort=name&brand[]=1&brand[]=6&brand[]=7&brand[]=3&brand[]=2&colour[]=10', $links->last()->attr('href'));

       $links = $crawler->filter('p.sort a');
       $this->assertEquals('?page=2&sort=first&brand[]=1&brand[]=6&brand[]=7&brand[]=3&brand[]=2', $links->first()->attr('href'));
       $this->assertEquals('?page=2&sort=high&brand[]=1&brand[]=6&brand[]=7&brand[]=3&brand[]=2', $links->last()->attr('href'));

       $links = $crawler->filter('p.pages a');
       $this->assertEquals('?page=1&sort=name&brand[]=1&brand[]=6&brand[]=7&brand[]=3&brand[]=2', $links->first()->attr('href'));
       $this->assertEquals('?page=6&sort=name&brand[]=1&brand[]=6&brand[]=7&brand[]=3&brand[]=2', $links->last()->attr('href'));
    }

    public function testShopWithColours()
    {
       $crawler = $this->client->request('GET', '/shop?page=2&sort=first&colour[]=5&colour[]=9&colour[]=7');

       $this->assertResponseIsSuccessful();
       $this->assertSelectorTextSame('h4', '21 Products');
       $this->assertEquals(6, $crawler->filter('div.product')->count());

       // Filter links
       $links = $crawler->filter('#sidebar a');
       $this->assertEquals('?page=2&sort=first&colour[]=9&colour[]=7', $links->first()->attr('href'));
       $this->assertEquals('?page=2&sort=first&colour[]=5&colour[]=9&colour[]=7&colour[]=10', $links->last()->attr('href'));

       // Sort links
       $links = $crawler->filter('p.sort a');
       $this->assertEquals('?page=2&sort=name&colour[]=5&colour[]=9&colour[]=7', $links->first()->attr('href'));
       $this->assertEquals('?page=2&sort=high&colour[]=5&colour[]=9&colour[]=7', $links->last()->attr('href'));

       // Page links
       $links = $crawler->filter('p.pages a');
       $this->assertEquals('?page=1&sort=first&colour[]=5&colour[]=9&colour[]=7', $links->first()->attr('href'));
       $this->assertEquals('?page=4&sort=first&colour[]=5&colour[]=9&colour[]=7', $links->last()->attr('href'));
    }

    public function testShopWithAll()
    {
       $crawler = $this->client->request('GET', '/shop?page=1&sort=low&category[]=2&category[]=3&brand[]=5&brand[]=3&colour[]=2');

       $this->assertResponseIsSuccessful();
       $this->assertSelectorTextSame('h4', '2 Products');
       $this->assertEquals(2, $crawler->filter('div.product')->count());

       // Filter links
       $links = $crawler->filter('#sidebar a');
       $this->assertEquals('?page=1&sort=low&category[]=3&brand[]=5&brand[]=3&colour[]=2', $links->first()->attr('href'));
       $this->assertEquals('?page=1&sort=low&category[]=2&category[]=3&brand[]=5&brand[]=3&colour[]=2&colour[]=10', $links->last()->attr('href'));

       // Sort links
       $links = $crawler->filter('p.sort a');
       $this->assertEquals('?page=1&sort=first&category[]=2&category[]=3&brand[]=5&brand[]=3&colour[]=2', $links->first()->attr('href'));
       $this->assertEquals('?page=1&sort=high&category[]=2&category[]=3&brand[]=5&brand[]=3&colour[]=2', $links->last()->attr('href'));
    }
    
}
