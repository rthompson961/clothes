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

        // login
        $crawler = $this->client->request('GET', '/login');
        $crawler = $this->client->submitForm('submit', [
            'email'    => 'user@user.com',
            'password' => 'pass'
        ]);
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

   /**
     * @dataProvider cardProvider
     */
    public function testPayment(string $card, string $route): void
    {
        $this->client->followRedirects();

        // add product
        $this->client->request('GET', '/basket/add/1/1');

        // select address
        $crawler = $this->client->request('GET', '/address/select');
        $crawler = $this->client->submitForm('address_select[submit]', [
            'address_select[address]' => '1'
        ]);

        // card info
        $crawler = $this->client->request('GET', '/checkout');
        $crawler = $this->client->submitForm('payment[submit]', [
            'payment[card]'   => $card,
            'payment[expiry]' => '1220',
            'payment[cvs]'    => '999',
        ]);

        $this->assertRouteSame($route);
    }

    public function cardProvider(): array
    {
        return [
            ['5424000000000015', 'shop'],
            ['5424000000000010', 'checkout'],
        ];
    }
}
