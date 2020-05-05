<?php

namespace App\Tests\Service;

use App\Service\ShopInterfaceBuilder;
use PHPUnit\Framework\TestCase;

class ShopInterfaceBuilderTest extends TestCase
{
    private ShopInterfaceBuilder $builder;
    private array $query;

    protected function setUp(): void
    {
        $this->builder = new ShopInterfaceBuilder();

        $this->query['page'] = 2;
        $this->query['sort'] = 'name';
        $this->query['filters'] = ['category' => [], 'brand' => [2, 5], 'colour' => [3]];
    }

    public function testFilterAttributes(): void
    {
        $choices = [
            [
                'id' => 1,
                'name' => 'blue'
            ],
            [
                'id' => 2,
                'name' => 'red'
            ],
            [
                'id' => 3,
                'name' => 'green'
            ]
        ];

        $result = $this->builder->getFilterAttributes('colour', $choices, $this->query);
        $expected = [
            [
                'id'     => 1,
                'name'   => 'blue',
                'active' => false,
                'url'    => '?page=2&sort=name&brand[]=2&brand[]=5&colour[]=3&colour[]=1'
            ],
            [
                'id'     => 2,
                'name'   => 'red',
                'active' => false,
                'url'    => '?page=2&sort=name&brand[]=2&brand[]=5&colour[]=3&colour[]=2'
            ],
            [
                'id'     => 3,
                'name'   => 'green',
                'active' => true,
                'url'    => '?page=2&sort=name&brand[]=2&brand[]=5'
            ]
        ];

        $this->assertTrue($result === $expected);
    }

    public function testSortOptions(): void
    {
        $choices = ['name', 'low', 'high'];

        $result = $this->builder->getSortOptions($choices, $this->query);
        $expected = [
            'name'  => null,
            'low'   => '?page=2&sort=low&brand[]=2&brand[]=5&colour[]=3',
            'high'  => '?page=2&sort=high&brand[]=2&brand[]=5&colour[]=3',
        ];

        $this->assertTrue($result === $expected);
    }

    public function testPageOptions(): void
    {
        $choices = [1, 2, 3];

        $result = $this->builder->getPageOptions($choices, $this->query);
        $expected = [
            1  => '?page=1&sort=name&brand[]=2&brand[]=5&colour[]=3',
            2  => null,
            3  => '?page=3&sort=name&brand[]=2&brand[]=5&colour[]=3'
        ];

        $this->assertTrue($result === $expected);
    }
}
