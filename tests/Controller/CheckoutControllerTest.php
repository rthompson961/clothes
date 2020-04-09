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

        $this->sandbox['card_success'] = '5424000000000015';
        $this->sandbox['card_failure'] = '5424000000000010';
        $this->sandbox['expiry']       = '1220';
        $this->sandbox['cvs']          = '999';

        // login
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('submit')->form();
        $form['email']    = 'user@user.com';
        $form['password'] = 'pass';
        $crawler = $this->client->submit($form);
    }

    public function pageProvider(): array
    {
        return [['address_select'], ['payment']];
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

    public function testAddressNewSuccess(): void
    {
        $crawler = $this->client->request('GET', '/address_new');

        $form = $crawler->selectButton('address_new[submit]')->form();
        $form['address_new[address1]'] = '1 street';
        $form['address_new[address2]'] = 'neighbourhood';
        $form['address_new[address3]'] = 'town';
        $form['address_new[county]']   = 'county';
        $form['address_new[postcode]'] = 'ab123cd';
        $crawler = $this->client->submit($form);

        $this->assertResponseRedirects('/address_select');
    }

    public function assertionProvider(): array
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

   /**
     * @dataProvider assertionProvider
     */
    public function testAddressNewAssertions(array $vals, string $error, int $count): void
    {
        $crawler = $this->client->request('GET', '/address_new');

        $form = $crawler->selectButton('address_new[submit]')->form();
        $form['address_new[address1]'] = $vals['address1'];
        $form['address_new[address2]'] = $vals['address2'];
        $form['address_new[county]']   = $vals['county'];
        $form['address_new[postcode]'] = $vals['postcode'];
        $crawler = $this->client->submit($form);

        $this->assertSelectorTextSame('#address_new li', $error);
        $this->assertEquals($count, $crawler->filter('#address_new  li')->count());
    }

    public function testAddressSelectSuccess(): void
    {
        // add product to basket
        $this->client->request('GET', '/add/1/1');

        $crawler = $this->client->request('GET', '/address_select');

        $form = $crawler->selectButton('address_select[submit]')->form();
        $form['address_select[address]'] = '1';
        $crawler = $this->client->submit($form);

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

        $form = $crawler->selectButton('address_select[submit]')->form();
        $form['address_select[address]'] = $missingId;
        $crawler = $this->client->submit($form);

        $this->expectException(InvalidArgumentException::class);
    }

    public function testPaymentSuccess(): void
    {
        // add product
        $this->client->request('GET', '/add/1/1');

        // select address
        $crawler = $this->client->request('GET', '/address_select');
        $form = $crawler->selectButton('address_select[submit]')->form();
        $form['address_select[address]'] = '1';
        $crawler = $this->client->submit($form);

        $crawler = $this->client->request('GET', '/payment');
        $form = $crawler->selectButton('payment[submit]')->form();
        $form['payment[card]']    = $this->sandbox['card_success'];
        $form['payment[expiry]']  = $this->sandbox['expiry'];
        $form['payment[cvs]']     = $this->sandbox['cvs'];
        $crawler = $this->client->submit($form);

        $this->assertResponseRedirects('/shop');
    }

    public function testPaymentFailure(): void
    {
        // add product
        $this->client->request('GET', '/add/1/1');

        // select address
        $crawler = $this->client->request('GET', '/address_select');
        $form = $crawler->selectButton('address_select[submit]')->form();
        $form['address_select[address]'] = '1';
        $crawler = $this->client->submit($form);

        $crawler = $this->client->request('GET', '/payment');
        $form = $crawler->selectButton('payment[submit]')->form();
        $form['payment[card]']    = $this->sandbox['card_failure'];
        $form['payment[expiry]']  = $this->sandbox['expiry'];
        $form['payment[cvs]']     = $this->sandbox['cvs'];
        $crawler = $this->client->submit($form);

        $this->assertRouteSame('payment');
    }
}
