<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BasketControllerTest extends WebTestCase
{
    private $client;

    protected function setUp()
    {
        $this->client = static::createClient();
    }

    public function testResponse()
    {
       $this->client->request('GET', '/basket');
       $this->assertResponseIsSuccessful();
    }

    public function testBasket()
    {
        // Add two products
        $crawler = $this->client->request('GET', '/product/2');
        $form = $crawler->selectButton('form[submit]')->form();
        $form['form[product]'] = 6;
        $crawler = $this->client->submit($form);
        $this->assertResponseRedirects('/basket');

        $crawler = $this->client->request('GET', '/product/24');
        $form = $crawler->selectButton('form[submit]')->form();
        $form['form[product]'] = 72;
        $crawler = $this->client->submit($form);
        $this->assertResponseRedirects('/basket');

        $crawler = $this->client->request('GET', '/basket');
        $this->assertEquals(4, $crawler->filter('tr')->count());
        $this->assertSelectorTextSame('th.total', '£71.98');

        // Empty basket
        $link = $crawler->filter('a.empty')->link();
        $crawler = $this->client->click($link);
        $this->assertResponseRedirects('/basket');
        $crawler = $this->client->request('GET', '/basket');
        $this->assertSelectorTextSame('p.empty', 'Your shopping basket is empty');
        $this->assertEquals(0, $crawler->filter('tr')->count());
    }
}
