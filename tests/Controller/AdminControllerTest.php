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

    /**
     * @dataProvider successProvider
     */
    public function testSuccess(string $page): void
    {
        $route = str_replace('_', '', $page);
        $val = 'New ' . str_replace('_', ' ', $page);

        $client = $this->login(static::createClient());
        $client->followRedirects();
        $client->request('GET', '/admin/' . $route);
        $client->submitForm($page . '[submit]', [
            $page . '[name]' => $val,
        ]);

        $repo['brand'] = static::$container->get(BrandRepository::class);
        $repo['product_group'] = static::$container->get(ProductGroupRepository::class);

        $object = $repo[$page]->findOneByName($val);

        $this->assertFalse(is_null($object));
        $this->assertRouteSame('admin_' . $route);
        $this->assertSelectorTextSame('p.flash', $val . ' added!');
    }

    public function successProvider(): array
    {
        return [
            [
                'brand'
            ],
            [
                'product_group'

            ]
        ];
    }

    /**
     * @dataProvider validationProvider
     */
    public function testValidation(string $page, string $field, string $error): void
    {
        $route = str_replace('_', '', $page);

        $client = $this->login(static::createClient());
        $client->request('GET', '/admin/' . $route);
        $client->submitForm($page . '[submit]', [
            $page . '[name]' => $field,
        ]);

        // error message matches
        $this->assertSelectorTextSame('#' . $page . ' li', $error);
    }

    public function validationProvider(): array
    {
        $longBrand = str_repeat('a', 16);
        $longGroup = str_repeat('a', 46);

        return [
            'Brand - Blank form' => [
                'brand',
                '',
                'This value should not be blank.'
            ],
            'Brand - Name too long' => [
                'brand',
                $longBrand,
                'This value is too long. It should have 15 characters or less.'
            ],
            'Group - Blank form' => [
                'product_group',
                '',
                'This value should not be blank.'
            ],
            'Group - Name too long' => [
                'product_group',
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
        return [['brand'], ['productgroup']];
    }
}
