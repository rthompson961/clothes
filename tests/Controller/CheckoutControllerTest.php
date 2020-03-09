<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CheckoutControllerTest extends WebTestCase
{
    public function testGuestResponse(): void
    {
        $client = static::createClient();

        $client->request('GET', '/checkout');
        $this->assertResponseRedirects('/login');
    }

    public function testUserResponse(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('submit')->form();
        $form['email'] = 'user@user.com';
        $form['password'] = 'pass';
        $crawler = $client->submit($form);

        // A product must be added to basket before accessing checkout
        $crawler = $client->request('GET', '/product/2');
        $form = $crawler->selectButton('form[submit]')->form();
        $form['form[product]'] = '6';
        $crawler = $client->submit($form);
        $this->assertResponseRedirects('/basket');

        $client->request('GET', '/checkout');
        $this->assertResponseIsSuccessful();
    }
}
