<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CheckoutControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private array $sandbox;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->sandbox['card_success'] = '5424000000000015';
        $this->sandbox['card_failure'] = '5424000000000010';
        $this->sandbox['expiry']       = '1220';
        $this->sandbox['cvs']          = '999';

        // login
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('submit')->form();
        $form['email']    = 'user@user.com';
        $form['password'] = 'pass';
        $crawler = $this->client->submit($form);
    }

    public function pageProvider(): array
    {
        return [['address_select'], ['payment']];
    }

   /**
     * @dataProvider pageProvider
     */
    public function testGuestRedirect(string $page): void
    {
        // destroy session
        $this->client->restart();

        $this->client->request('GET', '/' . $page);
        $this->assertResponseRedirects('/login');
    }

   /**
     * @dataProvider pageProvider
     */
    public function testNoBasketRedirect(string $page): void
    {
        $this->client->request('GET', '/' . $page);
        $this->assertResponseRedirects('/basket');
    }

    public function testCardFailure(): void
    {
        $this->markTestIncomplete('This test needs to be updated');

        // add product
        $this->client->request('GET', '/add/1/1');

        $crawler = $this->client->request('GET', '/checkout');
        $form = $crawler->selectButton('checkout[submit]')->form();
        $form['checkout[card]']    = $this->sandbox['card_failure'];
        $form['checkout[expiry]']  = $this->sandbox['expiry'];
        $form['checkout[cvs]']     = $this->sandbox['cvs'];
        $crawler = $this->client->submit($form);

        $this->assertRouteSame('checkout');
    }

    public function testCardSuccess(): void
    {
        $this->markTestIncomplete('This test needs to be updated');
        
        // add product
        $this->client->request('GET', '/add/1/1');

        $crawler = $this->client->request('GET', '/checkout');
        $form = $crawler->selectButton('checkout[submit]')->form();
        $form['checkout[card]']    = $this->sandbox['card_success'];
        $form['checkout[expiry]']  = $this->sandbox['expiry'];
        $form['checkout[cvs]']     = $this->sandbox['cvs'];
        $crawler = $this->client->submit($form);

        $this->assertResponseRedirects('/shop');
    }
}
