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

    /**
     * @dataProvider filterProvider
     */
    public function testFilterOptions(int $index, string $url, bool $active): void
    {
        $links = $this->shop->getFilterOptions(self::FILTERS, self::SORT, self::PAGE);

        $this->assertTrue($links['colour'][$index]->getUrl() === $url);
        $this->assertTrue($links['colour'][$index]->getActive() === $active);
    }

    public function filterProvider(): array
    {
        return [
            [
                'index'  => 0,
                'url'    => '/shop?page=2&sort=name&brand=4,5&colour=1,2,5',
                'active' => false
            ],
            [
                'index'  => 4,
                'url'    => '/shop?page=2&sort=name&brand=4,5&colour=2',
                'active' => true
            ]
        ];
    }

    /**
     * @dataProvider sortProvider
     */
    public function testSortOptions(int $index, string $url, bool $active): void
    {
        $links = $this->shop->getSortOptions(self::FILTERS, self::SORT, self::PAGE);

        $this->assertTrue($links[$index]['url'] === $url);
        $this->assertTrue($links[$index]['active'] === $active);
    }

    public function sortProvider(): array
    {
        return [
            [
                'index'  => 0,
                'url'    => '/shop?page=2&sort=first&brand=4,5&colour=2,5',
                'active' => false
            ],
            [
                'index'  => 1,
                'url'    => '/shop?page=2&sort=name&brand=4,5&colour=2,5',
                'active' => true
            ]
        ];
    }

    /**
     * @dataProvider pageProvider
     */
    public function testPageOptions(int $index, string $url, bool $active): void
    {
        $links = $this->shop->getPageOptions(self::FILTERS, self::SORT, self::PAGE, 3);

        $this->assertTrue($links[$index]['url'] === $url);
        $this->assertTrue($links[$index]['active'] === $active);
    }

    public function pageProvider(): array
    {
        return [
            [
                'index'  => 0,
                'url'    => '/shop?page=1&sort=name&brand=4,5&colour=2,5',
                'active' => false
            ],
            [
                'index'  => 1,
                'url'    => '/shop?page=2&sort=name&brand=4,5&colour=2,5',
                'active' => true
            ]
        ];
    }
}
