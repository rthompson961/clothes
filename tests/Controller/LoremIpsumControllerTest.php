<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoremIpsumControllerTest extends WebTestCase
{
    /**
     * @dataProvider pageProvider
     */
    public function testPages(string $page): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        $link = $crawler->filter('#footer a.' . $page)->link();
        $client->click($link);

        $this->assertResponseIsSuccessful();
    }

    public function pageProvider(): array
    {
        return [
            ['about'],
            ['privacy'],
            ['faq'],
            ['delivery'],
            ['terms'],
            ['returns']
        ];
    }
}
