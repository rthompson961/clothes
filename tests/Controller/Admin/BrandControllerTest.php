<?php

namespace App\Tests\Controller\Admin;

use App\Entity\Brand;
use App\Repository\BrandRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BrandControllerTest extends WebTestCase
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
        $client->request('GET', '/admin/brand');

        $client->submitForm('brand[submit]', [
            'brand[name]' => 'New brand'
        ]);

        $repo = static::$container->get(BrandRepository::class);
        $brand = $repo->findOneByName('New brand');

        $this->assertTrue($brand instanceof Brand);
        $this->assertRouteSame('admin_brand');
        $this->assertSelectorTextSame('p.flash', 'New brand added!');
    }

    /**
     * @dataProvider validationProvider
     */
    public function testValidation(string $name, string $error): void
    {
        $client = $this->login(static::createClient());
        $client->request('GET', '/admin/brand');

        $client->submitForm('brand[submit]', [
            'brand[name]' => $name
        ]);

        $this->assertSelectorTextSame('#brand li', $error);
    }

    public function validationProvider(): array
    {
        return [
            'Blank form' => [
                '',
                'This value should not be blank.'
            ],
            'Name too long' => [
                str_repeat('a', 16),
                'This value is too long. It should have 15 characters or less.'
            ]
        ];
    }

    public function testPageForbidden(): void
    {
        $client = $this->login(static::createClient(), 'user@user.com');
        $client->request('GET', '/admin/brand');

        $this->assertResponseStatusCodeSame(403);
    }
}
