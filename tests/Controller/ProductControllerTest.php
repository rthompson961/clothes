<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductControllerTest extends WebTestCase
{
    public function testGuestResponse()
    {
        $client = static::createClient();

        $client->request('GET', '/product/2');
        $this->assertResponseIsSuccessful();
    }
}
