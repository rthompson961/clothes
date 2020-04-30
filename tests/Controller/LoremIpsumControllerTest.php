<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoremIpsumControllerTest extends WebTestCase
{

   /**
     * @dataProvider pageProvider
     */
    public function testResponse(string $page): void
    {
        $client = static::createClient();
        $client->request('GET', '/' . $page);
        
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
