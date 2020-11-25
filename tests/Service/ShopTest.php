<?php

namespace App\Tests\Service;

use App\Service\Shop;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ShopTest extends WebTestCase
{
    private const SEARCH = null;
    private const FILTERS = ['category' => [], 'brand' => [4, 5], 'colour' => [2, 5]];
    private const SORT = 'name';
    private const PAGE = 2;
    private Shop $shop;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = self::$container;

        $router   = $container->get('router');
        $brand    = $container->get('App\Repository\BrandRepository');
        $category = $container->get('App\Repository\CategoryRepository');
        $colour   = $container->get('App\Repository\ColourRepository');
        $this->shop = new Shop($router, $brand, $category, $colour);
    }

    public function testFilterLinks(): void
    {
        $links = $this->shop->getFilterLinks(self::SEARCH, self::FILTERS, self::SORT, self::PAGE);

        // active filter to be removed
        $this->assertTrue($links['colour'][0]->getId() === 2);
        $this->assertTrue($links['colour'][0]->getText() === 'Black');
        $url = '/shop?sort=name&brand=4,5&colour=5';
        $this->assertTrue($links['colour'][0]->getUrl() === $url);
        $this->assertTrue($links['colour'][0]->getActive() === true);

        // inactive filter to be applied
        $this->assertTrue($links['colour'][1]->getId() === 1);
        $this->assertTrue($links['colour'][1]->getText() === 'Blue');
        $url = '/shop?sort=name&brand=4,5&colour=1,2,5';
        $this->assertTrue($links['colour'][1]->getUrl() === $url);
        $this->assertTrue($links['colour'][1]->getActive() === false);
    }

    public function testSortLinks(): void
    {
        $links = $this->shop->getSortLinks(self::SEARCH, self::FILTERS, self::SORT, self::PAGE);

        $url = '/shop?sort=first&brand=4,5&colour=2,5';
        $this->assertTrue($links[0]['url'] == $url);
        $this->assertTrue($links[0]['active'] === false);

        $url = '/shop?sort=name&brand=4,5&colour=2,5';
        $this->assertTrue($links[1]['url'] == $url);
        $this->assertTrue($links[1]['active'] === true);
    }

    public function testPageLinks(): void
    {
        $links = $this->shop->getPageLinks(self::SEARCH, self::FILTERS, self::SORT, self::PAGE, 3);

        $url = '/shop?page=1&sort=name&brand=4,5&colour=2,5';
        $this->assertTrue($links[0]['url'] == $url);
        $this->assertTrue($links[0]['active'] === false);

        $url = '/shop?page=2&sort=name&brand=4,5&colour=2,5';
        $this->assertTrue($links[1]['url'] == $url);
        $this->assertTrue($links[1]['active'] === true);
    }
}
