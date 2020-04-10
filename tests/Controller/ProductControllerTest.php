<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductControllerTest extends WebTestCase
{
    public function testForm(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/product/1');
        $form = $crawler->selectButton('product[submit]')->form();
        $form['product[product]'] = '1';
        $crawler = $client->submit($form);
        $this->assertResponseRedirects('/add/1/1');
    }
}
