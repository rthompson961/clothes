<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

class UserControllerTest extends WebTestCase
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
        return [['address/add'], ['address/select']];
    }

    public function testNoBasketRedirect(): void
    {
        $this->client->request('GET', '/address/select');

        $this->assertResponseRedirects('/basket');
    }

    public function testAddAddressSuccess(): void
    {
        $crawler = $this->client->request('GET', '/address/add');
        $crawler = $this->client->submitForm('address_add[submit]', [
            'address_add[address1]' => '1 street',
            'address_add[address2]' => 'neighbourhood',
            'address_add[address3]' => 'town',
            'address_add[county]'   => 'county',
            'address_add[postcode]' => 'ab123cd',
        ]);

        $this->assertResponseRedirects('/address/select');
    }

   /**
     * @dataProvider validationProvider
     */
    public function testAddAddressValidation(array $vals, string $error, int $count): void
    {
        $crawler = $this->client->request('GET', '/address/add');
        $crawler = $this->client->submitForm('address_add[submit]', [
            'address_add[address1]' => $vals['address1'],
            'address_add[address2]' => $vals['address2'],
            'address_add[county]'   => $vals['county'],
            'address_add[postcode]' => $vals['postcode'],
        ]);

        $this->assertSelectorTextSame('#address_add li', $error);
        $this->assertEquals($count, $crawler->filter('#address_add  li')->count());
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
}
