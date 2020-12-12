<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CheckoutControllerTest extends WebTestCase
{
    private function login(KernelBrowser $client): KernelBrowser
    {
        $repo = static::$container->get(UserRepository::class);
        $user = $repo->findOneByEmail('user@user.com');
        $client->loginUser($user);

        return $client;
    }

    /**
     * @dataProvider cardProvider
     */
    public function testPayment(string $card, string $route): void
    {
        $client = $this->login(static::createClient());
        $client->followRedirects();

        // add product
        $client->request('GET', '/basket/add/1/1');

        // select address
        $client->request('GET', '/address/select');
        $client->submitForm('address_select[submit]', [
            'address_select[address]' => '1'
        ]);

        // card info
        $client->request('GET', '/checkout');
        $client->submitForm('payment[submit]', [
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

    public function testGuestRedirect(): void
    {
        $client = static::createClient();
        $client->request('GET', '/checkout');

        $this->assertResponseRedirects('/login');
    }

    public function testNoBasketRedirect(): void
    {
        $client = $this->login(static::createClient());
        $client->request('GET', '/checkout');

        $this->assertResponseRedirects('/basket');
    }
}
