<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CheckoutControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testGuestRedirect(): void
    {
        $this->client->request('GET', '/checkout');
        $this->assertResponseRedirects('/login');
    }

    public function testNoBasketRedirect(): void
    {
        // login
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('submit')->form();
        $form['email']    = 'user@user.com';
        $form['password'] = 'pass';
        $crawler = $this->client->submit($form);

        $this->client->request('GET', '/checkout');
        $this->assertResponseRedirects('/basket');
    }

    public function testCardFailure(): object
    {
        // login
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('submit')->form();
        $form['email']    = 'user@user.com';
        $form['password'] = 'pass';
        $crawler = $this->client->submit($form);

        // add product
        $crawler = $this->client->request('GET', '/product/1');
        $form = $crawler->selectButton('form[submit]')->form();
        $form['form[product]'] = '1';
        $crawler = $this->client->submit($form);

        // add another product
        $crawler = $this->client->request('GET', '/product/1');
        $form = $crawler->selectButton('form[submit]')->form();
        $form['form[product]'] = '2';
        $crawler = $this->client->submit($form);

        // checkout with incorrect card number
        $sandbox['card']   = '5424000000000010';
        $sandbox['expiry'] = '1220';
        $sandbox['cvs']    = '999';

        $crawler = $this->client->request('GET', '/checkout');
        $form = $crawler->selectButton('form[submit]')->form();
        $form['form[card]']    = $sandbox['card'];
        $form['form[expiry]']  = $sandbox['expiry'];
        $form['form[cvs]']     = $sandbox['cvs'];
        $crawler = $this->client->submit($form);
        $this->assertRouteSame('checkout');

        return $this->client->getContainer()->get('session');
    }

    /**
     * @depends testCardFailure
     */
    public function testCardSuccess(object $session): void
    {
        $basket = $session->get('basket');
        $this->client->getContainer()->get('session')->set('basket', $basket);

        // login
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('submit')->form();
        $form['email']    = 'user@user.com';
        $form['password'] = 'pass';
        $crawler = $this->client->submit($form);

        // checkout with incorrect card number
        $sandbox['card']   = '5424000000000015';
        $sandbox['expiry'] = '1220';
        $sandbox['cvs']    = '999';

        $crawler = $this->client->request('GET', '/checkout');
        $form = $crawler->selectButton('form[submit]')->form();
        $form['form[card]']    = $sandbox['card'];
        $form['form[expiry]']  = $sandbox['expiry'];
        $form['form[cvs]']     = $sandbox['cvs'];
        $crawler = $this->client->submit($form);

        $this->assertResponseRedirects('/shop');
    }
}
