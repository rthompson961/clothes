<?php

namespace App\Tests\Controller\Admin;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ProductControllerTest extends WebTestCase
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
        $client->request('GET', '/admin/product');

        $client->submitForm('product[submit]', [
            'product[name]'     => 'New product',
            'product[price]'    => '9999',
            'product[category]' => '1',
            'product[brand]'    => '1',
            'product[colour]'   => '1'
        ]);

        $repo = static::$container->get(ProductRepository::class);
        $product = $repo->findOneByName('New product');

        $this->assertTrue($product instanceof Product);
        $this->assertRouteSame('admin_product');
        $this->assertSelectorTextSame('p.flash', 'New product added!');
    }

    /**
     * @dataProvider validationProvider
     */
    public function testValidation(array $fields, string $error): void
    {
        $client = $this->login(static::createClient());
        $client->request('GET', '/admin/product');

        $client->submitForm('product[submit]', [
            'product[name]'     => $fields['name'],
            'product[price]'    => $fields['price'],
            'product[category]' => '1',
            'product[brand]'    => '1',
            'product[colour]'   => '1'
        ]);

        $this->assertSelectorTextSame('#product li', $error);
    }

    public function validationProvider(): array
    {
        return [
            'Blank form' => [
                [
                    'name'  => '',
                    'price' => ''
                ],
                'This value should not be blank.',
            ],
            'Name too long' => [
                [
                    'name'  => str_repeat('a', 46),
                    'price' => '9999'
                ],
                'This value is too long. It should have 45 characters or less.',
            ],
            'Price too long' => [
                [
                    'name'  => 'My new product',
                    'price' => '9999999'
                ],
                'This value is too long. It should have 6 characters or less.'
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
        $client->request('GET', '/admin/product');

        $client->submitForm('product[submit]', [
            'product[name]'     => $fields['name'],
            'product[price]'    => $fields['price'],
            'product[category]' => $fields['category'],
            'product[brand]'    => $fields['brand'],
            'product[colour]'   => $fields['colour']
        ]);

        $this->expectException(InvalidArgumentException::class);
    }

    public function choiceProvider(): array
    {
        $validName = 'My new product';
        $validPrice = '99999';

        return [
            'Invalid Category' => [
                [
                    'name'     => $validName,
                    'price'    => $validPrice,
                    'category' => '99',
                    'brand'    => '1',
                    'colour'   => '1'
                ]
            ],
            'Invalid Brand' => [
                [
                    'name'  => $validName,
                    'price' => $validPrice,
                    'category' => '1',
                    'brand' => '99',
                    'colour'   => '1'
                ]
            ],
            'Invalid Colour' => [
                [
                    'name'  => $validName,
                    'price' => $validPrice,
                    'category' => '1',
                    'brand' => '1',
                    'colour' => '99'
                ]
            ]
        ];
    }

    public function testFileUploadSuccess(): void
    {
        $client = $this->login(static::createClient());
        $client->request('GET', '/admin/product');

        $file = new UploadedFile(
            __DIR__ . '/../../../public/img/product/testuploadsuccess.jpg',
            'testuploadsuccess.jpg'
        );

        $client->submitForm('product[submit]', [
            'product[image]'    => $file,
            'product[name]'     => 'New product',
            'product[price]'    => '9999',
            'product[category]' => '1',
            'product[brand]'    => '1',
            'product[colour]'   => '1',
        ]);

        $this->assertResponseRedirects('/admin/product');
    }

    /**
     * @dataProvider uploadProvider
     */
    public function testFileUploadFailure(string $filename, string $error): void
    {
        $client = $this->login(static::createClient());
        $client->request('GET', '/admin/product');

        $file = new UploadedFile(
            __DIR__ . '/../../../public/img/product/' . $filename,
            $filename
        );

        $client->submitForm('product[submit]', [
            'product[image]'    => $file,
            'product[name]'     => 'New product',
            'product[price]'    => '9999',
            'product[category]' => '1',
            'product[brand]'    => '1',
            'product[colour]'   => '1',
        ]);

        $this->assertSelectorTextSame('#product li', $error);
    }

    public function uploadProvider(): array
    {
        return [
            [
                'testuploadsize.jpg',
                'The file is too large, maximum size is 50 kB.'
            ],
            [
                'testuploadtype.png',
                'Please upload a valid JPG image.'
            ]
        ];
    }

    public function testPageForbidden(): void
    {
        $client = $this->login(static::createClient(), 'user@user.com');
        $client->request('GET', '/admin/product');

        $this->assertResponseStatusCodeSame(403);
    }
}
