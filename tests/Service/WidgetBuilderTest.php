<?php

namespace App\Tests\Service;

use App\Service\WidgetBuilder;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Router;
use Doctrine\ORM\EntityManagerInterface;

class WidgetBuilderTest extends WebTestCase
{
    private WidgetBuilder $widget;
    private array $query;

    protected function setUp(): void
    {
        $client = static::createClient();
        $em = $client->getContainer()->get('doctrine.orm.entity_manager');
        $router = $client->getContainer()->get('router');
        $this->widget = new WidgetBuilder($em, $router);

        $this->query['page'] = 2;
        $this->query['sort'] = 'name';
        $this->query['filters'] = ['category' => [], 'brand' => [2, 5], 'colour' => [3]];
    }

    public function testFilterAttributes(): void
    {
        $result = $this->widget->getFilterOptions('colour', $this->query);
        $expected = [
            [
                'id'     => 2,
                'name'   => 'Black',
                'active' => false,
                'url'    => '/shop?page=2&sort=name&brand=2,5&colour=3,2'
            ],
            [
                'id'     => 1,
                'name'   => 'Blue',
                'active' => false,
                'url'    => '/shop?page=2&sort=name&brand=2,5&colour=3,1'
            ],
            [
                'id'     => 4,
                'name'   => 'Grey',
                'active' => false,
                'url'    => '/shop?page=2&sort=name&brand=2,5&colour=3,4'
            ],
            [
                'id'     => 5,
                'name'   => 'Navy',
                'active' => false,
                'url'    => '/shop?page=2&sort=name&brand=2,5&colour=3,5'
            ],
            [
                'id'     => 3,
                'name'   => 'Olive',
                'active' => true,
                'url'    => '/shop?page=2&sort=name&brand=2,5'
            ],
            [
                'id'     => 6,
                'name'   => 'Orange',
                'active' => false,
                'url'    => '/shop?page=2&sort=name&brand=2,5&colour=3,6'
            ],
            [
                'id'     => 7,
                'name'   => 'Plum',
                'active' => false,
                'url'    => '/shop?page=2&sort=name&brand=2,5&colour=3,7'
            ],
            [
                'id'     => 8,
                'name'   => 'Red',
                'active' => false,
                'url'    => '/shop?page=2&sort=name&brand=2,5&colour=3,8'
            ],
            [
                'id'     => 9,
                'name'   => 'Stone',
                'active' => false,
                'url'    => '/shop?page=2&sort=name&brand=2,5&colour=3,9'
            ],
            [
                'id'     => 10,
                'name'   => 'White',
                'active' => false,
                'url'    => '/shop?page=2&sort=name&brand=2,5&colour=3,10'
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
