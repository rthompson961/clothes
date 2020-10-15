<?php

namespace App\Tests\Service;

use App\Service\Shop;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Router;

class ShopTest extends WebTestCase
{
    private const FILTERS = ['category' => [], 'brand' => [4, 5], 'colour' => [2, 5]];
    private const SORT = 'name';
    private const PAGE = 2;

    private Shop $shop;

    protected function setUp(): void
    {
        $client = static::createClient();
        $em = $client->getContainer()->get('doctrine.orm.entity_manager');
        $router = $client->getContainer()->get('router');
        $this->shop = new Shop($em, $router);
    }

    public function testFilterOptions(): void
    {
        $links = $this->shop->getFilterOptions(self::FILTERS, self::SORT, self::PAGE);

        $url = '/shop?page=2&sort=name&brand=4,5&colour=1,2,5';
        $this->assertTrue($links['colour'][0]->getUrl() == $url);
        $this->assertTrue($links['colour'][0]->getActive() === false);

        $url = '/shop?page=2&sort=name&brand=4,5&colour=2';
        $this->assertTrue($links['colour'][4]->getUrl() == $url);
        $this->assertTrue($links['colour'][4]->getActive() == true);
    }

    public function testSortOptions(): void
    {
        $links = $this->shop->getSortOptions(self::FILTERS, self::SORT, self::PAGE);

        $url = '/shop?page=2&sort=first&brand=4,5&colour=2,5';
        $this->assertTrue($links[0]['url'] == $url);
        $this->assertTrue($links[0]['active'] === false);

        $url = '/shop?page=2&sort=name&brand=4,5&colour=2,5';
        $this->assertTrue($links[1]['url'] == $url);
        $this->assertTrue($links[1]['active'] === true);
    }

    public function testPageOptions(): void
    {
        $links = $this->shop->getPageOptions(self::FILTERS, self::SORT, self::PAGE, 3);

        $url = '/shop?page=1&sort=name&brand=4,5&colour=2,5';
        $this->assertTrue($links[0]['url'] == $url);
        $this->assertTrue($links[0]['active'] === false);

        $url = '/shop?page=2&sort=name&brand=4,5&colour=2,5';
        $this->assertTrue($links[1]['url'] == $url);
        $this->assertTrue($links[1]['active'] === true);
    }
}
