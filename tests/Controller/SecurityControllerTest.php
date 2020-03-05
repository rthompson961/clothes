<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    private $client;

    protected function setUp()
    {
        $this->client = static::createClient();
    }

    public function testResponse()
    {
        $this->client->request('GET', '/login');
        $this->assertResponseIsSuccessful();
    }

    public function testCorrectLogin()
    {
        $crawler = $this->client->request('GET', '/login');

        $form = $crawler->selectButton('submit')->form();
        $form['email'] = 'user@user.com';
        $form['password'] = 'pass';
        $crawler = $this->client->submit($form);

        $this->assertResponseRedirects('/');
    }

    public function testIncorrectLogin()
    {
        $crawler = $this->client->request('GET', '/login');

        $form = $crawler->selectButton('submit')->form();
        $form['email'] = 'incorrect@user.com';
        $form['password'] = 'wrong';
        $crawler = $this->client->submit($form);

        $this->assertRouteSame('app_login');
    }
}
