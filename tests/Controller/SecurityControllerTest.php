<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testResponse(): void
    {
        $this->client->request('GET', '/login');
        $this->assertResponseIsSuccessful();
    }

    public function testCorrectLogin(): void
    {
        $crawler = $this->client->request('GET', '/login');

        $form = $crawler->selectButton('submit')->form();
        $form['email'] = 'user@user.com';
        $form['password'] = 'pass';
        $crawler = $this->client->submit($form);

        $this->assertResponseRedirects('/');
    }

    public function testIncorrectLogin(): void
    {
        $crawler = $this->client->request('GET', '/login');

        $form = $crawler->selectButton('submit')->form();
        $form['email'] = 'incorrect@user.com';
        $form['password'] = 'wrong';
        $crawler = $this->client->submit($form);

        $this->assertRouteSame('login');
    }
}
