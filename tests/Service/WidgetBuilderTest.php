<?php

namespace App\Tests\Service;

use App\Service\WidgetBuilder;
use PHPUnit\Framework\TestCase;

class WidgetBuilderTest extends TestCase
{
    private WidgetBuilder $widget;
    private array $query;

    protected function setUp(): void
    {
        $this->widget = new WidgetBuilder();

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

        $result = $this->widget->getFilterAttributes('colour', $choices, $this->query);
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
        $result = $this->widget->getSortOptions($this->query);
        $expected = [
            'First In' => '?page=2&sort=first&brand[]=2&brand[]=5&colour[]=3',
            'Name'  => null,
            'Lowest Price'   => '?page=2&sort=low&brand[]=2&brand[]=5&colour[]=3',
            'Highest Price'  => '?page=2&sort=high&brand[]=2&brand[]=5&colour[]=3',
        ];

        $this->assertTrue($result === $expected);
    }

    public function testPageOptions(): void
    {
        $result = $this->widget->getPageOptions(3, $this->query);
        $expected = [
            1  => '?page=1&sort=name&brand[]=2&brand[]=5&colour[]=3',
            2  => null,
            3  => '?page=3&sort=name&brand[]=2&brand[]=5&colour[]=3'
        ];

        $this->assertTrue($result === $expected);
    }
}
