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
    }

    public function testAdd(): object
    {
        // Add two of product item 1 and one of product item 2
        $values = [
            ['id' => '1', 'quantity' => '7'],
            ['id' => '1', 'quantity' => '3'],
            ['id' => '2', 'quantity' => '1'],
        ];

        foreach ($values as $val) {
            $crawler = $this->client->request('GET', '/product/1');
            $form = $crawler->selectButton('form[submit]')->form();
            $form['form[product]'] = $val['id'];
            $form['form[quantity]'] = $val['quantity'];
            $crawler = $this->client->submit($form);
            $this->assertResponseRedirects('/basket');
        }
        $crawler = $this->client->request('GET', '/basket');
        $this->assertSelectorTextSame('a.basket', '11 Items');
        $this->assertSelectorTextSame('th.total', '£549.89');

        return $this->client->getContainer()->get('session');
    }

    /**
     * @depends testAdd
     */
    public function testRemove(object $session): void
    {
        $basket = $session->get('basket');
        $count  = $session->get('basket_count');
        $this->client->getContainer()->get('session')->set('basket', $basket);
        $this->client->getContainer()->get('session')->set('basket_count', $count);

        $crawler = $this->client->request('GET', '/basket');
        $this->assertSelectorTextSame('a.basket', '11 Items');
        $this->assertSelectorTextSame('th.total', '£549.89');
        $link = $crawler->filter('a.remove')->link();
        $crawler = $this->client->click($link);
        $this->assertResponseRedirects('/basket');
        $crawler = $this->client->request('GET', '/basket');
        $this->assertSelectorTextSame('a.basket', '1 Item');
        $this->assertSelectorTextSame('th.total', '£49.99');
    }

    /**
     * @depends testAdd
     */
    public function testEmpty(object $session): void
    {
        $basket = $session->get('basket');
        $count  = $session->get('basket_count');
        $this->client->getContainer()->get('session')->set('basket', $basket);
        $this->client->getContainer()->get('session')->set('basket_count', $count);

        $crawler = $this->client->request('GET', '/basket');
        $this->assertSelectorTextSame('a.basket', '11 Items');
        $this->assertSelectorTextSame('th.total', '£549.89');
        $link = $crawler->filter('a.empty')->link();
        $crawler = $this->client->click($link);
        $this->assertResponseRedirects('/basket');
        $crawler = $this->client->request('GET', '/basket');
        $this->assertSelectorTextSame('a.basket', '0 Items');
        $this->assertSelectorTextSame('p.empty', 'Your shopping basket is empty');
    }
}
