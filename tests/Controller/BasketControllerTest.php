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

        // Add ten of unit 1 and one of unit 2
        $values = [
            ['id' => '1', 'quantity' => '7'],
            ['id' => '1', 'quantity' => '3'],
            ['id' => '2', 'quantity' => '1'],
        ];
        foreach ($values as $val) {
            $this->client->request('GET', "/basket_add/{$val['id']}/{$val['quantity']}");
        }
    }

    public function testAdd(): void
    {
        $crawler = $this->client->request('GET', '/basket');

        $this->assertSelectorTextSame('a.basket', '11 Items');
        $this->assertSelectorTextSame('th.total', '£549.89');
    }

    public function testRemove(): void
    {
        $crawler = $this->client->request('GET', '/basket');
        $link = $crawler->filter('a.remove')->link();
        $crawler = $this->client->click($link);

        $this->assertResponseRedirects('/basket');

        $crawler = $this->client->request('GET', '/basket');

        $this->assertSelectorTextSame('a.basket', '1 Item');
        $this->assertSelectorTextSame('th.total', '£49.99');

        // remove last item
        $link = $crawler->filter('a.remove')->link();
        $crawler = $this->client->click($link);
        $crawler = $this->client->request('GET', '/basket');

        $this->assertSelectorTextSame('p.empty', 'Your shopping basket is empty');
    }

    public function testEmpty(): void
    {
        $crawler = $this->client->request('GET', '/basket');
        $link = $crawler->filter('a.empty')->link();
        $crawler = $this->client->click($link);

        $this->assertResponseRedirects('/basket');

        $crawler = $this->client->request('GET', '/basket');
        
        $this->assertSelectorTextSame('a.basket', '0 Items');
        $this->assertSelectorTextSame('p.empty', 'Your shopping basket is empty');
    }
}
