<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

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

   /**
     * @dataProvider membersOnlyPageProvider
     */
    public function testGuestRedirect(string $page): void
    {
        // destroy session
        $this->client->restart();
        $this->client->request('GET', '/' . $page);

        $this->assertResponseRedirects('/login');
    }

    public function membersOnlyPageProvider(): array
    {
        return [['address/add'], ['address/select'], ['payment']];
    }

   /**
     * @dataProvider nonEmptyBasketPageProvider
     */
    public function testNoBasketRedirect(string $page): void
    {
        $this->client->request('GET', '/' . $page);

        $this->assertResponseRedirects('/basket');
    }

    public function nonEmptyBasketPageProvider(): array
    {
        return [['address/select'], ['payment']];
    }

    public function testSelectAddressSuccess(): void
    {
        // add product to basket
        $this->client->request('GET', '/basket/add/1/1');

        $crawler = $this->client->request('GET', '/address/select');
        $crawler = $this->client->submitForm('address_select[submit]', [
            'address_select[address]' => '1'
        ]);

        $this->assertResponseRedirects('/payment');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSelectAddressFailure(): void
    {
        $missingId = '3'; // 1 = data fixture, 2 = added in earlier test

        // add product to basket
        $this->client->request('GET', '/basket/add/1/1');
        $crawler = $this->client->request('GET', '/address/select');
        $crawler = $this->client->submitForm('address_select[submit]', [
            'address_select[address]' => $missingId
        ]);

        $this->expectException(InvalidArgumentException::class);
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
        $crawler = $this->client->request('GET', '/payment');
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
            ['5424000000000010', 'payment'],
        ];
    }
}
