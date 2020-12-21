<?php

namespace App\Tests\Controller\Admin;

use App\Entity\ProductUnit;
use App\Repository\ProductUnitRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

class ProductUnitControllerTest extends WebTestCase
{
    private function login(
        KernelBrowser $client,
        string $email = 'admin@admin.com'
    ): KernelBrowser {
        $repo = static::$container->get(UserRepository::class);
        $user = $repo->findOneByEmail($email);
        $client->loginUser($user);

        return $client;
    }

    public function testSuccess(): void
    {
        $client = $this->login(static::createClient());
        $client->followRedirects();
        $client->request('GET', '/admin/unit');

        $client->submitForm('product_unit[submit]', [
            'product_unit[stock]'   => '100',
            'product_unit[product]' => '1',
            'product_unit[size]'    => '1'
        ]);

        $repo = static::$container->get(ProductUnitRepository::class);
        $unit = $repo->find(198);

        $this->assertTrue($unit instanceof ProductUnit);
        $this->assertRouteSame('admin_unit');
        $this->assertSelectorTextSame('p.flash', 'New product unit added!');
    }

    /**
     * @dataProvider validationProvider
     */
    public function testValidation(string $stock, string $error): void
    {
        $client = $this->login(static::createClient());
        $client->request('GET', '/admin/unit');

        $client->submitForm('product_unit[submit]', [
            'product_unit[stock]'   => $stock,
        ]);

        $this->assertSelectorTextSame('#product_unit li', $error);
    }

    public function validationProvider(): array
    {
        return [
            'Blank form' => [
                '',
                'This value should not be blank.',
            ],
            'Stock too long' => [
                '99999',
                'This value is too long. It should have 4 characters or less.',
            ]
        ];
    }

    /**
     * @dataProvider choiceProvider
     * @expectedException InvalidArgumentException
     */
    public function testEntityChoices(array $fields): void
    {
        $client = $this->login(static::createClient());
        $client->request('GET', '/admin/unit');

        $client->submitForm('product_unit[submit]', [
            'product_unit[stock]'   => $fields['stock'],
            'product_unit[product]' => $fields['product'],
            'product_unit[size]'    => $fields['size'],
        ]);

        $this->expectException(InvalidArgumentException::class);
    }

    public function choiceProvider(): array
    {
        return [
            'Invalid Product' => [
                [
                    'stock'   => '999',
                    'product' => '999',
                    'size'    => '1'
                ]
            ],
            'Invalid Size' => [
                [
                    'stock'   => '999',
                    'product' => '1',
                    'size'    => '4'
                ]
            ]
        ];
    }

    public function testPageForbidden(): void
    {
        $client = $this->login(static::createClient(), 'user@user.com');
        $client->request('GET', '/admin/unit');

        $this->assertResponseStatusCodeSame(403);
    }
}
