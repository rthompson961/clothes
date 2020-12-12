<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

class UserControllerTest extends WebTestCase
{
    private function login(KernelBrowser $client): KernelBrowser
    {
        $repo = static::$container->get(UserRepository::class);
        $user = $repo->findOneByEmail('user@user.com');
        $client->loginUser($user);

        return $client;
    }

    public function testAddAddressSuccess(): void
    {
        $client = $this->login(static::createClient());

        $client->request('GET', '/address/add');
        $client->submitForm('address_add[submit]', [
            'address_add[address1]' => '1 street',
            'address_add[address2]' => 'neighbourhood',
            'address_add[address3]' => 'town',
            'address_add[county]'   => 'county',
            'address_add[postcode]' => 'ab123cd',
        ]);

        $this->assertResponseRedirects('/checkout');
    }

    /**
     * @dataProvider validationProvider
     */
    public function testAddAddressValidation(array $fields, string $error, int $count): void
    {
        $client = $this->login(static::createClient());

        $client->request('GET', '/address/add');
        $crawler = $client->submitForm('address_add[submit]', [
            'address_add[address1]' => $fields['address1'],
            'address_add[address2]' => $fields['address2'],
            'address_add[county]'   => $fields['county'],
            'address_add[postcode]' => $fields['postcode'],
        ]);

        // error message matches
        $this->assertSelectorTextSame('#address_add li', $error);
        // correct amount of errors
        $this->assertEquals($count, $crawler->filter('#address_add li')->count());
    }

    public function validationProvider(): array
    {
        $longAddress  = str_repeat("a", 51);
        $longPostcode = str_repeat("a", 16);

        return [
            'Blank form' => [
                [
                    'address1' => '',
                    'address2' => '',
                    'county'   => '',
                    'postcode' => ''
                ],
                'This value should not be blank.',
                4
            ],
            'Address too long' => [
                [
                    'address1' => $longAddress,
                    'address2' => $longAddress,
                    'county'   => $longAddress,
                    'postcode' => 'ab123cd'
                ],
                'This value is too long. It should have 50 characters or less.',
                3
            ],
            'Postcode too long' => [
                [
                    'address1' => 'house',
                    'address2' => 'street',
                    'county'   => 'county',
                    'postcode' => $longPostcode
                ],
                'This value is too long. It should have 15 characters or less.',
                1
            ],
        ];
    }

    public function testSelectAddressSuccess(): void
    {
        $client = $this->login(static::createClient());

        $client->request('GET', '/address/select');
        $client->submitForm('address_select[submit]', [
            'address_select[address]' => '1'
        ]);

        $this->assertResponseRedirects('/checkout');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSelectAddressFailure(): void
    {
        $client = $this->login(static::createClient());

        // 1 = data fixture, 2 = does not exist
        $id = '2';
        $client->request('GET', '/address/select');
        $client->submitForm('address_select[submit]', [
            'address_select[address]' => $id
        ]);

        $this->expectException(InvalidArgumentException::class);
    }

    public function testOrderList(): void
    {
        $client = $this->login(static::createClient());
        $crawler = $client->request('GET', '/orders');

        $this->assertResponseIsSuccessful();
        $this->assertEquals(1, $crawler->filter('div.order')->count());
    }

    public function testOrder(): void
    {
        $client = $this->login(static::createClient());
        $crawler = $client->request('GET', '/order/1');

        $this->assertResponseIsSuccessful();
        $this->assertPageTitleSame('Items - Order #1 | Clothes Shop');
        $this->assertSelectorTextSame('h2', 'Items - Order #1');
        $this->assertEquals(4, $crawler->filter('div.order-item')->count());
    }

    public function testOrderBelongsToUser(): void
    {
        $client = $this->login(static::createClient());
        $client->request('GET', '/order/2');

        $this->assertResponseRedirects('/orders');
    }

    /**
     * @dataProvider protectedPageProvider
     */
    public function testGuestRedirect(string $page): void
    {
        $client = static::createClient();
        $client->request('GET', '/' . $page);

        $this->assertResponseRedirects('/login');
    }

    public function protectedPageProvider(): array
    {
        return [['address/add'], ['address/select'], ['orders'], ['order/1']];
    }
}
