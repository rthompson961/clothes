<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CheckoutControllerTest extends WebTestCase
{
    public function testRedirect(): void
    {
        $client = static::createClient();

        $client->request('GET', '/checkout');
        $this->assertResponseRedirects('/login');
    }

    public function testCheckout(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('submit')->form();
        $form['email'] = 'user@user.com';
        $form['password'] = 'pass';
        $crawler = $client->submit($form);

        $crawler = $client->request('GET', '/product/1');
        $form = $crawler->selectButton('form[submit]')->form();
        $form['form[product]'] = '1';
        $crawler = $client->submit($form);
        $this->assertResponseRedirects('/basket');

        $crawler = $client->request('GET', '/product/1');
        $form = $crawler->selectButton('form[submit]')->form();
        $form['form[product]'] = '2';
        $crawler = $client->submit($form);
        $this->assertResponseRedirects('/basket');

        $client->request('GET', '/checkout');
        $this->assertResponseIsSuccessful();
    }
}
