<?php

namespace App\Tests\Controller;

use App\Entity\Brand;
use App\Entity\ProductGroup;
use App\Repository\BrandRepository;
use App\Repository\ProductGroupRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

class AdminControllerTest extends WebTestCase
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

    public function testBrandSuccess(): void
    {
        $client = $this->login(static::createClient());
        $client->followRedirects();
        $name = 'My new brand';
        $client->request('GET', '/admin/brand');
        $client->submitForm('brand[submit]', [
            'brand[name]' => $name,
        ]);

        $repo = static::$container->get(BrandRepository::class);
        $brand = $repo->findOneByName($name);

        $this->assertTrue($brand instanceof Brand);
        $this->assertRouteSame('admin_brand');
        $this->assertSelectorTextSame('p.flash', 'New brand added!');
    }

    /**
     * @dataProvider brandValidationProvider
     */
    public function testBrandValidation(string $name, string $error): void
    {
        $client = $this->login(static::createClient());
        $client->request('GET', '/admin/brand');
        $client->submitForm('brand[submit]', [
            'brand[name]' => $name,
        ]);

        // error message matches
        $this->assertSelectorTextSame('#brand li', $error);
    }

    public function brandValidationProvider(): array
    {
        $longName = str_repeat('a', 16);

        return [
            'Blank form' => [
                '',
                'This value should not be blank.'
            ],
            'Name too long' => [
                $longName,
                'This value is too long. It should have 15 characters or less.'
            ]
        ];
    }

    public function testGroupSuccess(): void
    {
        $client = $this->login(static::createClient());
        $client->followRedirects();
        $name = 'My new group';
        $client->request('GET', '/admin/group');
        $client->submitForm('product_group[submit]', [
            'product_group[name]' => $name,
        ]);

        $repo = static::$container->get(ProductGroupRepository::class);
        $group = $repo->findOneByName($name);

        $this->assertTrue($group instanceof ProductGroup);
        $this->assertRouteSame('admin_group');
        $this->assertSelectorTextSame('p.flash', 'New product group added!');
    }

    /**
     * @dataProvider groupValidationProvider
     */
    public function testGroupValidation(string $name, string $error): void
    {
        $client = $this->login(static::createClient());
        $client->request('GET', '/admin/group');
        $client->submitForm('product_group[submit]', [
            'product_group[name]' => $name,
        ]);

        // error message matches
        $this->assertSelectorTextSame('#product_group li', $error);
    }

    public function groupValidationProvider(): array
    {
        $longGroup = str_repeat('a', 46);

        return [
            'Blank form' => [
                '',
                'This value should not be blank.'
            ],
            'Name too long' => [
                $longGroup,
                'This value is too long. It should have 45 characters or less.'
            ]
        ];
    }

    /**
     * @dataProvider forbiddenPageProvider
     */
    public function testForbidden(string $page): void
    {
        $client = $this->login(static::createClient(), 'user@user.com');
        $client->request('GET', '/admin/' . $page);

        $this->assertResponseStatusCodeSame(403);
    }

    public function forbiddenPageProvider(): array
    {
        return [['brand']];
    }
}
