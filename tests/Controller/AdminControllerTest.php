<?php

namespace App\Tests\Controller;

use App\Entity\Brand;
use App\Repository\BrandRepository;
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
        $name = 'My new brand';
        $client->request('GET', '/admin/brand');
        $client->submitForm('brand[submit]', [
            'brand[name]' => $name,
        ]);

        $repo = static::$container->get(BrandRepository::class);
        $brand = $repo->findOneByName($name);

        $this->assertTrue($brand instanceof Brand);
        $this->assertResponseRedirects('/admin/brand');
    }

    /**
     * @dataProvider validationProvider
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

    public function validationProvider(): array
    {
        $longName = str_repeat("a", 16);

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
