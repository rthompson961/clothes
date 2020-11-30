<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BasketControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->followRedirects();
        
        // Add 3 of unit 1 and 1 of unit 2
        $this->client->request('GET', '/basket/add/1/3');
        $this->client->request('GET', '/basket/add/2/1');
    }

    public function testAdd(): void
    {
        $this->client->request('GET', '/basket');

        $this->assertSelectorTextSame('a.basket', '4 Items');
        $this->assertSelectorTextSame('th.total', '£199.96');
    }

    public function testRemove(): void
    {
        // remove unit 1
        $crawler = $this->client->request('GET', '/basket');
        $link = $crawler->filter('a.remove')->link();
        $this->client->click($link);

        $this->assertSelectorTextSame('a.basket', '1 Items');
        $this->assertSelectorTextSame('th.total', '£49.99');

        // remove unit 2
        $crawler = $this->client->request('GET', '/basket');
        $link = $crawler->filter('a.remove')->link();
        $this->client->click($link);

        $this->assertSelectorTextSame('p.empty', 'Your shopping basket is empty');
    }

    public function testEmpty(): void
    {
        $crawler = $this->client->request('GET', '/basket');
        $link = $crawler->filter('a.empty')->link();
        $this->client->click($link);

        $this->assertSelectorTextSame('a.basket', '0 Items');
        $this->assertSelectorTextSame('p.empty', 'Your shopping basket is empty');
    }
}
