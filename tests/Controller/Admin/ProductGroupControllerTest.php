<?php

namespace App\Tests\Controller\Admin;

use App\Entity\ProductGroup;
use App\Repository\ProductGroupRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductGroupControllerTest extends WebTestCase
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
        $client->request('GET', '/admin/group');

        $client->submitForm('product_group[submit]', [
            'product_group[name]' => 'New product group'
        ]);

        $repo = static::$container->get(ProductGroupRepository::class);
        $group = $repo->findOneByName('New product group');

        $this->assertTrue($group instanceof ProductGroup);
        $this->assertRouteSame('admin_group');
        $this->assertSelectorTextSame('p.flash', 'New product group added!');
    }

    /**
     * @dataProvider validationProvider
     */
    public function testValidation(string $name, string $error): void
    {
        $client = $this->login(static::createClient());
        $client->request('GET', '/admin/group');

        $client->submitForm('product_group[submit]', [
            'product_group[name]' => $name
        ]);

        $this->assertSelectorTextSame('#product_group li', $error);
    }

    public function validationProvider(): array
    {
        return [
            'Blank form' => [
                '',
                'This value should not be blank.'
            ],
            'Name too long' => [
                str_repeat('a', 46),
                'This value is too long. It should have 45 characters or less.'
            ]
        ];
    }

    public function testPageForbidden(): void
    {
        $client = $this->login(static::createClient(), 'user@user.com');
        $client->request('GET', '/admin/group');

        $this->assertResponseStatusCodeSame(403);
    }
}
