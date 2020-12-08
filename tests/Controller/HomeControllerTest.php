<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    /**
     * @dataProvider categoryProvider
     */
    public function testNavLinks(string $category): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        $link = $crawler->filter('#nav a.' . $category)->link();
        $client->click($link);

        $this->assertResponseIsSuccessful();
    }

    /**
     * @dataProvider categoryProvider
     */
    public function testHomeLinks(string $category): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        $link = $crawler->filter('#home a.' . $category)->link();
        $client->click($link);

        $this->assertResponseIsSuccessful();
    }

    public function categoryProvider(): array
    {
        return [
            ['all'],
            ['jackets'],
            ['fleeces'],
            ['parkas'],
            ['sweatshirts'],
            ['hoodies'],
            ['t-shirts'],
            ['jeans']
        ];
    }
}
