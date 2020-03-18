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

    public function testAdd(): array
    {
        // Add two of product item 1 and one of product item 2
        foreach (['1', '1', '2'] as $id) {
            $crawler = $this->client->request('GET', '/product/1');
            $form = $crawler->selectButton('form[submit]')->form();
            $form['form[product]'] = $id;
            $crawler = $this->client->submit($form);
            $this->assertResponseRedirects('/basket');
        }
        $crawler = $this->client->request('GET', '/basket');
        $this->assertSelectorTextSame('th.total', '£149.97');

        return $this->client->getContainer()->get('session')->get('basket');
    }

    /**
     * @depends testAdd
     */
    public function testRemove(array $basket): void
    {
        $this->client->getContainer()->get('session')->set('basket', $basket);

        $crawler = $this->client->request('GET', '/basket');
        $link = $crawler->filter('a.remove')->link();
        $crawler = $this->client->click($link);
        $this->assertResponseRedirects('/basket');
        $crawler = $this->client->request('GET', '/basket');
        $this->assertSelectorTextSame('th.total', '£49.99');
    }

    /**
     * @depends testAdd
     */
    public function testEmpty(array $basket): void
    {
        $this->client->getContainer()->get('session')->set('basket', $basket);

        $crawler = $this->client->request('GET', '/basket');
        $link = $crawler->filter('a.empty')->link();
        $crawler = $this->client->click($link);
        $this->assertResponseRedirects('/basket');
        $crawler = $this->client->request('GET', '/basket');
        $this->assertSelectorTextSame('p.empty', 'Your shopping basket is empty');
    }
}
