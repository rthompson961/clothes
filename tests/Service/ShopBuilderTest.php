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

        $this->query['page'] = 2;
        $this->query['sort'] = 'name';
        $this->query['filters'] = ['category' => [], 'brand' => [2, 5], 'colour' => [3]];
    }

    public function testFilterOptions(): void
    {
        $list['colour'] = [
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

        $result = $this->builder->getFilterOptions('colour', $list, $this->query);
        $expected = [
            [
                'text'   => 'red',
                'active' => false,
                'url'    => '/shop?page=2&sort=name&brand[0]=2&brand[1]=5&colour[0]=3&colour[1]=1'
            ],
            [
                'text'   => 'blue',
                'active' => false,
                'url'    => '/shop?page=2&sort=name&brand[0]=2&brand[1]=5&colour[0]=3&colour[1]=2'
            ],
            [
                'text'   => 'green',
                'active' => true,
                'url'    => '/shop?page=2&sort=name&brand[0]=2&brand[1]=5'
            ]
        ];

        $this->assertTrue($result === $expected);
    }

    public function testSortOptions(): void
    {
        $result = $this->builder->getSortOptions(['first', 'name', 'low', 'high'], $this->query);
        $expected = [
            [
                'text'   => 'First',
                'active' => false,
                'url'    => '/shop?page=2&sort=first&brand[0]=2&brand[1]=5&colour[0]=3'
            ],
            [
                'text'   => 'Name',
                'active' => true,
                'url'    => '/shop?page=2&sort=name&brand[0]=2&brand[1]=5&colour[0]=3'
            ],
            [
                'text'   => 'Low',
                'active' => false,
                'url'    => '/shop?page=2&sort=low&brand[0]=2&brand[1]=5&colour[0]=3'
            ],
            [
                'text'   => 'High',
                'active' => false,
                'url'    => '/shop?page=2&sort=high&brand[0]=2&brand[1]=5&colour[0]=3'
            ]
        ];

        $this->assertTrue($result === $expected);
    }

    public function testPageOptions(): void
    {
        $result = $this->builder->getPageOptions(3, $this->query);
        $expected = [
            [
                'text'   => 1,
                'active' => false,
                'url'    => '/shop?page=1&sort=name&brand[0]=2&brand[1]=5&colour[0]=3'
            ],
            [
                'text'   => 2,
                'active' => true,
                'url'    => '/shop?page=2&sort=name&brand[0]=2&brand[1]=5&colour[0]=3'
            ],
            [
                'text'   => 3,
                'active' => false,
                'url'    => '/shop?page=3&sort=name&brand[0]=2&brand[1]=5&colour[0]=3'
            ]
        ];

        $this->assertTrue($result === $expected);
    }
}
