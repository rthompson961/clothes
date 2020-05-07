<?php

namespace App\Tests\Service;

use App\Service\WidgetBuilder;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Router;

class WidgetBuilderTest extends WebTestCase
{
    private WidgetBuilder $widget;
    private array $query;

    protected function setUp(): void
    {
        $client = static::createClient();
        if ($client->getContainer() === null) {
            throw new \Exception('Could not get service container');
        }
        $router = $client->getContainer()->get('router');
        $this->widget = new WidgetBuilder($router);

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
                'url'    => '/shop?page=2&sort=name&brand=2,5&colour=1,3'
            ],
            [
                'id'     => 2,
                'name'   => 'red',
                'active' => false,
                'url'    => '/shop?page=2&sort=name&brand=2,5&colour=2,3'
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
        $result = $this->widget->getSortOptions($this->query);
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
        $result = $this->widget->getPageOptions(3, $this->query);
        $expected = [
            1  => '/shop?page=1&sort=name&brand=2,5&colour=3',
            2  => null,
            3  => '/shop?page=3&sort=name&brand=2,5&colour=3'
        ];

        $this->assertTrue($result === $expected);
    }
}
