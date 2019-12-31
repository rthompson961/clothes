<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MarketControllerTest extends WebTestCase
{
    public function testMarket()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/market');
        $this->assertResponseIsSuccessful();
        $this->assertEquals(8, $crawler->filter('div.ebay')->count());
    }
}
