<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoremIpsumControllerTest extends WebTestCase
{

   /**
     * @dataProvider pageProvider
     */
    public function testResponse($page)
    {
        $client = static::createClient();
        $client->request('GET', '/' . $page);
        $this->assertResponseIsSuccessful();
    }

    public function pageProvider()
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
