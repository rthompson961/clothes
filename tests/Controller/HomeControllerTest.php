<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    public function testHeaderLink(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $link = $crawler->filter('h1 a')->link();
        $crawler = $client->click($link);

        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('home');
    }

    /**
     * @dataProvider linkProvider
     */
    public function testShopLinks(string $name, int $count): void
    {
        $client = static::createClient();

        // nav bar
        $crawler = $client->request('GET', '/');
        $link = $crawler->filter('#nav a.' . $name)->link();
        $crawler = $client->click($link);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextSame('h4', $count . ' Products');

        // home
        $crawler = $client->request('GET', '/');
        $link = $crawler->filter('#home a.' . $name)->link();
        $crawler = $client->click($link);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextSame('h4', $count . ' Products');
    }

    public function linkProvider(): array
    {
        return [
            ['all', 66],
            ['jackets', 13],
            ['fleeces', 4],
            ['parkas', 4],
            ['sweatshirts', 9],
            ['hoodies', 10],
            ['t-shirts', 14],
            ['jeans', 12]
        ];
    }
}
