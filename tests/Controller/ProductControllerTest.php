<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductControllerTest extends WebTestCase
{
    public function testProduct()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/product/2');
        $this->assertResponseIsSuccessful();
        $form = $crawler->selectButton('form[submit]')->form();
        $form['form[product]'] = 6;
        $crawler = $client->submit($form);
        $this->assertResponseRedirects('/basket');
    }
}
