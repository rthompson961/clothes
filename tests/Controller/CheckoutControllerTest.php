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

    public function testGuestRedirect(): void
    {
        // destroy session
        $this->client->restart();

        $this->client->request('GET', '/checkout');
        $this->assertResponseRedirects('/login');
    }

    public function testNoBasketRedirect(): void
    {
        $this->client->request('GET', '/checkout');
        $this->assertResponseRedirects('/basket');
    }

    public function testCardFailure(): void
    {
        // add product
        $crawler = $this->client->request('GET', '/product/1');
        $form = $crawler->selectButton('form[submit]')->form();
        $form['form[product]'] = '1';
        $crawler = $this->client->submit($form);

        $crawler = $this->client->request('GET', '/checkout');
        $form = $crawler->selectButton('form[submit]')->form();
        $form['form[card]']    = $this->sandbox['card_failure'];
        $form['form[expiry]']  = $this->sandbox['expiry'];
        $form['form[cvs]']     = $this->sandbox['cvs'];
        $crawler = $this->client->submit($form);

        $this->assertRouteSame('checkout');
    }

    public function testCardSuccess(): void
    {
        // add product
        $crawler = $this->client->request('GET', '/product/1');
        $form = $crawler->selectButton('form[submit]')->form();
        $form['form[product]'] = '1';
        $crawler = $this->client->submit($form);

        $crawler = $this->client->request('GET', '/checkout');
        $form = $crawler->selectButton('form[submit]')->form();
        $form['form[card]']    = $this->sandbox['card_success'];
        $form['form[expiry]']  = $this->sandbox['expiry'];
        $form['form[cvs]']     = $this->sandbox['cvs'];
        $crawler = $this->client->submit($form);

        $this->assertResponseRedirects('/shop');
    }
}
