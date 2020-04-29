<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

class CheckoutControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private array $sandbox;

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
     * @dataProvider pageProvider
     */
    public function testGuestRedirect(string $page): void
    {
        // destroy session
        $this->client->restart();
        $this->client->request('GET', '/' . $page);

        $this->assertResponseRedirects('/login');
    }

   /**
     * @dataProvider pageProvider
     */
    public function testNoBasketRedirect(string $page): void
    {
        $this->client->request('GET', '/' . $page);

        $this->assertResponseRedirects('/basket');
    }

    public function pageProvider(): array
    {
        return [['address_select'], ['payment']];
    }

    public function testAddressNewSuccess(): void
    {
        $crawler = $this->client->request('GET', '/address_new');
        $crawler = $this->client->submitForm('address_new[submit]', [
            'address_new[address1]' => '1 street',
            'address_new[address2]' => 'neighbourhood',
            'address_new[address3]' => 'town',
            'address_new[county]'   => 'county',
            'address_new[postcode]' => 'ab123cd',
        ]);

        $this->assertResponseRedirects('/address_select');
    }

   /**
     * @dataProvider validationProvider
     */
    public function testAddressNewValidation(array $vals, string $error, int $count): void
    {
        $crawler = $this->client->request('GET', '/address_new');
        $crawler = $this->client->submitForm('address_new[submit]', [
            'address_new[address1]' => $vals['address1'],
            'address_new[address2]' => $vals['address2'],
            'address_new[county]'   => $vals['county'],
            'address_new[postcode]' => $vals['postcode'],
        ]);

        $this->assertSelectorTextSame('#address_new li', $error);
        $this->assertEquals($count, $crawler->filter('#address_new  li')->count());
    }

    public function validationProvider(): array
    {
        $max['text'] = 50;
        $max['postcode'] = 15;
        $overflow = str_repeat("a", $max['text'] + 1);

        return [
            [
                [
                    'address1' => '',
                    'address2' => '',
                    'county'   => '',
                    'postcode' => ''
                ],
                'This value should not be blank.',
                4
            ],
            [
                [
                    'address1' => $overflow,
                    'address2' => $overflow,
                    'county'   => $overflow,
                    'postcode' => 'ab123cd'
                ],
                'This value is too long. It should have 50 characters or less.',
                3
            ],
            [
                [
                    'address1' => 'house',
                    'address2' => 'street',
                    'county'   => 'county',
                    'postcode' => str_repeat("a", $max['postcode'] + 1)
                ],
                'This value is too long. It should have 15 characters or less.',
                1
            ],
        ];
    }

    public function testAddressSelectSuccess(): void
    {
        // add product to basket
        $this->client->request('GET', '/add/1/1');
        $crawler = $this->client->request('GET', '/address_select');
        $crawler = $this->client->submitForm('address_select[submit]', [
            'address_select[address]' => '1'
        ]);

        $this->assertResponseRedirects('/payment');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testAddressSelectFailure(): void
    {
        $missingId = '3'; // 1 = data fixture, 2 = added in earlier test

        // add product to basket
        $this->client->request('GET', '/add/1/1');
        $crawler = $this->client->request('GET', '/address_select');
        $crawler = $this->client->submitForm('address_select[submit]', [
            'address_select[address]' => $missingId
        ]);

        $this->expectException(InvalidArgumentException::class);
    }

   /**
     * @dataProvider cardProvider
     */
    public function testPayment(array $card, string $route): void
    {
        $this->client->followRedirects();

        // add product
        $this->client->request('GET', '/add/1/1');

        // select address
        $crawler = $this->client->request('GET', '/address_select');
        $crawler = $this->client->submitForm('address_select[submit]', [
            'address_select[address]' => '1'
        ]);
        
        // card info
        $crawler = $this->client->request('GET', '/payment');
        $crawler = $this->client->submitForm('payment[submit]', [
            'payment[card]'   => $card['number'],
            'payment[expiry]' => $card['expiry'],
            'payment[cvs]'    => $card['cvs'],
        ]);

        $this->assertRouteSame($route);
    }

    public function cardProvider(): array
    {
        return [
            [
                ['number' => '5424000000000015', 'expiry' => '1220', 'cvs' => '999'],
                 'shop'
            ], 
            [
                ['number' => '5424000000000010', 'expiry' => '1220', 'cvs' => '999'],
                'payment'
            ], 
        ];
    }
}
