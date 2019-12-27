<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CheckoutControllerTest extends WebTestCase
{
    public function testGuestResponse()
    {
        $client = static::createClient();

        $client->request('GET', '/checkout');
        $this->assertResponseRedirects('/login');
    }

    public function testUserResponse()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('submit')->form();
        $form['email'] = 'user@user.com';
        $form['password'] = 'pass';
        $crawler = $client->submit($form);

        $client->request('GET', '/checkout');
        $this->assertResponseIsSuccessful();
    }
}
