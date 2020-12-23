<?php

namespace App\Tests\Service;

use App\Service\Shop;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ShopTest extends WebTestCase
{
    private const SEARCH = null;
    private const FILTERS = ['category' => [], 'brand' => [4, 5], 'colour' => [2, 4]];
    private const SORT = 'name';
    private const PAGE = 2;
    private Shop $shop;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = self::$container;

        $router   = $container->get('router');
        $this->shop = new Shop($router);
    }

    public function testFilterLinks(): void
    {
        $options['colour'][1] = 'Red';
        $options['colour'][2] = 'Blue';
        $url['red'] = '/shop?sort=name&brand=4,5&colour=1,2,4';
        $url['blue'] = '/shop?sort=name&brand=4,5&colour=4';

        $links = $this->shop->getFilterLinks(self::SEARCH, self::FILTERS, self::SORT, $options);

        // inactive filter to be applied
        $this->assertTrue($links['colour'][0]->getId() === 1);
        $this->assertTrue($links['colour'][0]->getText() === 'Red');
        $this->assertTrue($links['colour'][0]->getUrl() === $url['red']);
        $this->assertTrue($links['colour'][0]->getActive() === false);

        // active filter to be removed
        $this->assertTrue($links['colour'][1]->getId() === 2);
        $this->assertTrue($links['colour'][1]->getText() === 'Blue');
        $this->assertTrue($links['colour'][1]->getUrl() === $url['blue']);
        $this->assertTrue($links['colour'][1]->getActive() === true);
    }

    public function testSortLinks(): void
    {
        $links = $this->shop->getSortLinks(self::SEARCH, self::FILTERS, self::SORT, self::PAGE);

        $url = '/shop?sort=first&brand=4,5&colour=2,4';
        $this->assertTrue($links[0]['url'] == $url);
        $this->assertTrue($links[0]['active'] === false);

        $url = '/shop?sort=name&brand=4,5&colour=2,4';
        $this->assertTrue($links[1]['url'] == $url);
        $this->assertTrue($links[1]['active'] === true);
    }

    public function testPageLinks(): void
    {
        $links = $this->shop->getPageLinks(self::SEARCH, self::FILTERS, self::SORT, self::PAGE, 3);

        $url = '/shop?page=1&sort=name&brand=4,5&colour=2,4';
        $this->assertTrue($links[0]['url'] == $url);
        $this->assertTrue($links[0]['active'] === false);

        $url = '/shop?page=2&sort=name&brand=4,5&colour=2,4';
        $this->assertTrue($links[1]['url'] == $url);
        $this->assertTrue($links[1]['active'] === true);
    }
}
