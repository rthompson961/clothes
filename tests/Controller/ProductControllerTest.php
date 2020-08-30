<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductControllerTest extends WebTestCase
{
    public function testForm(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/product/1');
        $crawler = $client->submitForm('product[submit]', [
            'product[product]' => '1'
        ]);

        $this->assertResponseRedirects('/basket/add/1/1');
    }
}
