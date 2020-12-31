<?php

namespace App\Tests\Service;

use App\Service\Shop;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ShopTest extends WebTestCase
{
    private Shop $shop;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = self::$container;
        $router = $container->get('router');
        $this->shop = new Shop($router);
    }

    public function testFilterLinks(): void
    {
        $key = 'colour';
        $data = [
            [
                'id'   => 1,
                'name' => 'Red'
            ],
            [
                'id'   => 2,
                'name' => 'Blue'
            ],
            [
                'id'   => 3,
                'name' => 'Yellow'
            ]
        ];
        $filters = [
            'category' => [],
            'brand'    => [],
            'colour'   => [3]
        ];
        $sort = 'name';
        $search = null;

        $links = $this->shop->getFilterLinks($key, $data, $filters, $sort, $search);

        $this->assertTrue($links[0]['text']   === 'Red');
        $this->assertTrue($links[0]['active'] === false);
        $this->assertTrue($links[0]['url']    === '/shop?sort=name&colour=1,3');

        $this->assertTrue($links[1]['text']   === 'Blue');
        $this->assertTrue($links[1]['active'] === false);
        $this->assertTrue($links[1]['url']    === '/shop?sort=name&colour=2,3');

        $this->assertTrue($links[2]['text']   === 'Yellow');
        $this->assertTrue($links[2]['active'] === true);
        $this->assertTrue($links[2]['url']    === '/shop?sort=name');
    }

    public function testSortLinks(): void
    {
        $search = null;
        $filters = [
            'category' => [],
            'brand'    => [2, 4],
            'colour'   => []
        ];
        $sort = 'name';

        $links = $this->shop->getSortLinks($search, $filters, $sort);

        $this->assertTrue($links[0]['text'] === 'First');
        $this->assertTrue($links[0]['active'] === false);
        $this->assertTrue($links[0]['url'] === '/shop?sort=first&brand=2,4');

        $this->assertTrue($links[1]['text'] === 'Name');
        $this->assertTrue($links[1]['active'] === true);
        $this->assertTrue($links[1]['url'] === '/shop?sort=name&brand=2,4');

        $this->assertTrue($links[2]['text'] === 'Low');
        $this->assertTrue($links[2]['active'] === false);
        $this->assertTrue($links[2]['url'] === '/shop?sort=low&brand=2,4');

        $this->assertTrue($links[3]['text'] === 'High');
        $this->assertTrue($links[3]['active'] === false);
        $this->assertTrue($links[3]['url'] === '/shop?sort=high&brand=2,4');
    }

    public function testPageLinks(): void
    {
        $search = null;
        $filters = [
            'category' => [],
            'brand'    => [2, 4],
            'colour'   => []
        ];
        $sort = 'name';
        $page = 3;
        $pageCount = 3;

        $links = $this->shop->getPageLinks($search, $filters, $sort, $page, $pageCount);

        $this->assertTrue($links[0]['text'] === '< 1');
        $this->assertTrue($links[0]['active'] === false);
        $this->assertTrue($links[0]['url'] == '/shop?page=1&sort=name&brand=2,4');

        $this->assertTrue($links[1]['text'] === 2);
        $this->assertTrue($links[1]['active'] === false);
        $this->assertTrue($links[1]['url'] == '/shop?page=2&sort=name&brand=2,4');

        $this->assertTrue($links[2]['text'] === '3 >');
        $this->assertTrue($links[2]['active'] === true);
        $this->assertTrue($links[2]['url'] == '/shop?page=3&sort=name&brand=2,4');
    }
}
