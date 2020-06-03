<?php

namespace App\Tests\Service;

use App\Service\ShopBuilder;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Router;

class ShopBuilderTest extends WebTestCase
{
    private ShopBuilder $builder;
    private array $query;

    protected function setUp(): void
    {
        $client = static::createClient();
        $router = $client->getContainer()->get('router');
        $this->builder = new ShopBuilder($router);

        $query['page'] = 2;
        $query['sort'] = 'name';
        $query['filters'] = ['category' => [], 'brand' => [2, 5], 'colour' => [3]];
        $this->builder->setQuery($query);
    }

    public function testFilterAttributes(): void
    {
        $choices = [
            [
                'id' => 1,
                'name' => 'red'
            ],
            [
                'id' => 2,
                'name' => 'blue'
            ],
            [
                'id' => 3,
                'name' => 'green'
            ]
        ];

        $result = $this->builder->getFilterOptions('colour', $choices);
        $expected = [
            [
                'id'     => 1,
                'name'   => 'red',
                'active' => false,
                'url'    => '/shop?page=2&sort=name&brand=2,5&colour=3,1'
            ],
            [
                'id'     => 2,
                'name'   => 'blue',
                'active' => false,
                'url'    => '/shop?page=2&sort=name&brand=2,5&colour=3,2'
            ],
            [
                'id'     => 3,
                'name'   => 'green',
                'active' => true,
                'url'    => '/shop?page=2&sort=name&brand=2,5'
            ]
        ];

        $this->assertTrue($result === $expected);
    }

    public function testSortOptions(): void
    {
        $result = $this->builder->getSortOptions();
        $expected = [
            'First In' => '/shop?page=2&sort=first&brand=2,5&colour=3',
            'Name'  => null,
            'Lowest Price'   => '/shop?page=2&sort=low&brand=2,5&colour=3',
            'Highest Price'  => '/shop?page=2&sort=high&brand=2,5&colour=3',
        ];

        $this->assertTrue($result === $expected);
    }

    public function testPageOptions(): void
    {
        $result = $this->builder->getPageOptions(3);
        $expected = [
            1  => '/shop?page=1&sort=name&brand=2,5&colour=3',
            2  => null,
            3  => '/shop?page=3&sort=name&brand=2,5&colour=3'
        ];

        $this->assertTrue($result === $expected);
    }
}
