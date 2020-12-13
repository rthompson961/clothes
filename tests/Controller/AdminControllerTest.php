<?php

namespace App\Tests\Controller;

use App\Entity\Brand;
use App\Entity\Product;
use App\Entity\ProductGroup;
use App\Repository\BrandRepository;
use App\Repository\ProductRepository;
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
    public function testSuccess(string $page, array $vals): void
    {
        $route = str_replace('_', '', $page);

        $client = $this->login(static::createClient());
        $client->followRedirects();
        $client->request('GET', '/admin/' . $route);

        $fields = [];
        foreach ($vals as $key => $val) {
            $fields[$page . '[' . $key . ']'] = $val;
        }
        $client->submitForm($page . '[submit]', $fields);

        $repo['brand'] = static::$container->get(BrandRepository::class);
        $repo['product_group'] = static::$container->get(ProductGroupRepository::class);
        $repo['product'] = static::$container->get(ProductRepository::class);

        $object = $repo[$page]->findOneByName($vals['name']);

        $this->assertFalse(is_null($object));
        $this->assertRouteSame('admin_' . $route);
        $this->assertSelectorTextSame('p.flash', $vals['name'] . ' added!');
    }

    public function successProvider(): array
    {
        return [
            [
                'brand',
                [
                    'name' => 'New brand'
                ],
            ],
            [
                'product_group',
                [
                    'name' => 'New product group'
                ],
            ],
            [
                'product',
                [
                    'name'  => 'New product',
                    'price' => '9999',
                    'category' => '1',
                    'brand' => '1',
                    'colour' => '1'
                ],
            ],
            [
                'product',
                [
                    'name'  => 'New product',
                    'price' => '9999',
                    'category' => '1',
                    'brand' => '1',
                    'colour' => '1',
                    'product_group' => '1'
                ],
            ]
        ];
    }

    /**
     * @dataProvider validationProvider
     */
    public function testValidation(string $page, array $vals, string $error): void
    {
        $route = str_replace('_', '', $page);

        $client = $this->login(static::createClient());
        $client->request('GET', '/admin/' . $route);

        $fields = [];
        foreach ($vals as $key => $val) {
            $fields[$page . '[' . $key . ']'] = $val;
        }
        $client->submitForm($page . '[submit]', $fields);

        $this->assertSelectorTextSame('#' . $page . ' li', $error);
    }

    public function validationProvider(): array
    {
        $longBrand   = str_repeat('a', 16);
        $longGroup   = str_repeat('a', 46);
        $longProduct = str_repeat('a', 46);

        return [
            'Brand - Blank form' => [
                'brand',
                [
                    'name' => ''
                ],
                'This value should not be blank.'
            ],
            'Brand - Name too long' => [
                'brand',
                [
                    'name' => $longBrand
                ],
                'This value is too long. It should have 15 characters or less.'
            ],
            'Group - Blank form' => [
                'product_group',
                [
                    'name' => ''
                ],
                'This value should not be blank.'
            ],
            'Group - Name too long' => [
                'product_group',
                [
                    'name' => $longGroup
                ],
                'This value is too long. It should have 45 characters or less.'
            ],
            'Product - Blank form' => [
                'product',
                [
                    'name' => '',
                    'price' => ''
                ],
                'This value should not be blank.',
            ],
            'Product - Name too long' => [
                'product',
                [
                    'name' => $longProduct,
                    'price' => '9999'
                ],
                'This value is too long. It should have 45 characters or less.',
            ],
            'Product - Price too long' => [
                'product',
                [
                    'name' => 'My new product',
                    'price' => '999999999'
                ],
                'This value is too long. It should have 6 characters or less.'
            ]
        ];
    }

    /**
     * @dataProvider choiceProvider
     * @expectedException InvalidArgumentException
     */
    public function testEntityChoices(string $page, array $vals): void
    {
        $route = str_replace('_', '', $page);

        $client = $this->login(static::createClient());
        $client->request('GET', '/admin/' . $route);

        $fields = [];
        foreach ($vals as $key => $val) {
            $fields[$page . '[' . $key . ']'] = $val;
        }
        $client->submitForm($page . '[submit]', $fields);

        $this->expectException(InvalidArgumentException::class);
    }

    public function choiceProvider(): array
    {
        $validName = 'My new product';
        $validPrice = '9999';

        return [
            'Product - Invalid Category' => [
                'product',
                [
                    'name'     => $validName,
                    'price'    => $validPrice,
                    'category' => '99'
                ]
            ],
            'Product - Invalid Brand' => [
                'product',
                [
                    'name'  => $validName,
                    'price' => $validPrice,
                    'brand' => '99'
                ]
            ],
            'Product - Invalid Colour' => [
                'product',
                [
                    'name'  => $validName,
                    'price' => $validPrice,
                    'colour' => '99'
                ]
            ],
        ];
    }

    /**
     * @dataProvider forbiddenPageProvider
     */
    public function testForbiddenPages(string $page): void
    {
        $client = $this->login(static::createClient(), 'user@user.com');
        $client->request('GET', '/admin/' . $page);

        $this->assertResponseStatusCodeSame(403);
    }

    public function forbiddenPageProvider(): array
    {
        return [['brand'], ['productgroup'], ['product']];
    }
}
