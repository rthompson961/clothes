<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ShopControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testDefault(): void
    {
        $crawler = $this->client->request('GET', '/shop');

        $this->assertSelectorTextSame('h4', '66 Products');
        $this->assertEquals(6, $crawler->filter('div.product')->count());

        $links = $crawler->filter('#sidebar a');
        $this->assertEquals('/shop?page=1&sort=first&category=1', $links->first()->attr('href'));
        $this->assertEquals('/shop?page=1&sort=first&colour=10', $links->last()->attr('href'));

        $links = $crawler->filter('p.sort a');
        $this->assertEquals('/shop?page=1&sort=name', $links->first()->attr('href'));
        $this->assertEquals('/shop?page=1&sort=high', $links->last()->attr('href'));

        $links = $crawler->filter('p.pages a');
        $this->assertEquals('/shop?page=2&sort=first', $links->first()->attr('href'));
        $this->assertEquals('/shop?page=11&sort=first', $links->last()->attr('href'));
    }

    public function testWithPage(): void
    {
        $crawler = $this->client->request('GET', '/shop?page=5&sort=first');

        $this->assertSelectorTextSame('h4', '66 Products');
        $this->assertEquals(6, $crawler->filter('div.product')->count());

        $links = $crawler->filter('#sidebar a');
        $this->assertEquals('/shop?page=5&sort=first&category=1', $links->first()->attr('href'));
        $this->assertEquals('/shop?page=5&sort=first&colour=10', $links->last()->attr('href'));

        $links = $crawler->filter('p.sort a');
        $this->assertEquals('/shop?page=5&sort=name', $links->first()->attr('href'));
        $this->assertEquals('/shop?page=5&sort=high', $links->last()->attr('href'));

        $links = $crawler->filter('p.pages a');
        $this->assertEquals('/shop?page=1&sort=first', $links->first()->attr('href'));
        $this->assertEquals('/shop?page=11&sort=first', $links->last()->attr('href'));
    }

    public function testWithSort(): void
    {
        $crawler = $this->client->request('GET', '/shop?page=3&sort=low');

        $this->assertSelectorTextSame('h4', '66 Products');
        $this->assertEquals(6, $crawler->filter('div.product')->count());

        $links = $crawler->filter('#sidebar a');
        $this->assertEquals('/shop?page=3&sort=low&category=1', $links->first()->attr('href'));
        $this->assertEquals('/shop?page=3&sort=low&colour=10', $links->last()->attr('href'));

        $links = $crawler->filter('p.sort a');
        $this->assertEquals('/shop?page=3&sort=first', $links->first()->attr('href'));
        $this->assertEquals('/shop?page=3&sort=high', $links->last()->attr('href'));

        $links = $crawler->filter('p.pages a');
        $this->assertEquals('/shop?page=1&sort=low', $links->first()->attr('href'));
        $this->assertEquals('/shop?page=11&sort=low', $links->last()->attr('href'));
    }

    public function testWithFilters(): void
    {
        $crawler = $this->client->request('GET', '/shop?page=5&sort=name&category=6,7');

        $this->assertSelectorTextSame('h4', '26 Products');
        $this->assertEquals(2, $crawler->filter('div.product')->count());

        $links = $crawler->filter('#sidebar a');
        $this->assertEquals('/shop?page=5&sort=name&category=7', $links->first()->attr('href'));
        $this->assertEquals(
            '/shop?page=5&sort=name&category=6,7&colour=10',
            $links->last()->attr('href')
        );

        $links = $crawler->filter('p.sort a');
        $this->assertEquals('/shop?page=5&sort=first&category=6,7', $links->first()->attr('href'));
        $this->assertEquals('/shop?page=5&sort=high&category=6,7', $links->last()->attr('href'));

        $links = $crawler->filter('p.pages a');
        $this->assertEquals('/shop?page=1&sort=name&category=6,7', $links->first()->attr('href'));
        $this->assertEquals('/shop?page=4&sort=name&category=6,7', $links->last()->attr('href'));
    }
}
