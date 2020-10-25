<?php

namespace App\Tests\Service;

use App\Service\Shop;
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
        $router = $client->getContainer()->get('router');
        $this->shop = new Shop($router);
    }

    public function testFilterLinks(): void
    {
        $red = new Class {
            public function getId()
            {
                return 1;
            }

            public function getName()
            {

                return 'Red';
            }
        };

        $blue = new Class {
            public function getId()
            {
                return 2;
            }

            public function getName()
            {
                return 'Blue';
            }
        };

        $options['category'] = [];
        $options['brand']    = [];
        $options['colour']   = [$red, $blue];

        $links = $this->shop->getFilterLinks($options, self::FILTERS, self::SORT, self::PAGE);

        // inactive filter to be applied
        $this->assertTrue($links['colour'][0]->getId() === 1);
        $this->assertTrue($links['colour'][0]->getText() === 'Red');
        $url = '/shop?page=2&sort=name&brand=4,5&colour=1,2,5';
        $this->assertTrue($links['colour'][0]->getUrl() === $url);
        $this->assertTrue($links['colour'][0]->getActive() === false);

        // active filter to be removed
        $this->assertTrue($links['colour'][1]->getId() === 2);
        $this->assertTrue($links['colour'][1]->getText() === 'Blue');
        $url = '/shop?page=2&sort=name&brand=4,5&colour=5';
        $this->assertTrue($links['colour'][1]->getUrl() === $url);
        $this->assertTrue($links['colour'][1]->getActive() === true);
    }

    public function testSortLinks(): void
    {
        $links = $this->shop->getSortLinks(self::FILTERS, self::SORT, self::PAGE);

        $url = '/shop?page=2&sort=first&brand=4,5&colour=2,5';
        $this->assertTrue($links[0]['url'] == $url);
        $this->assertTrue($links[0]['active'] === false);

        $url = '/shop?page=2&sort=name&brand=4,5&colour=2,5';
        $this->assertTrue($links[1]['url'] == $url);
        $this->assertTrue($links[1]['active'] === true);
    }

    public function testPageLinks(): void
    {
        $links = $this->shop->getPageLinks(self::FILTERS, self::SORT, self::PAGE, 3);

        $url = '/shop?page=1&sort=name&brand=4,5&colour=2,5';
        $this->assertTrue($links[0]['url'] == $url);
        $this->assertTrue($links[0]['active'] === false);

        $url = '/shop?page=2&sort=name&brand=4,5&colour=2,5';
        $this->assertTrue($links[1]['url'] == $url);
        $this->assertTrue($links[1]['active'] === true);
    }
}
