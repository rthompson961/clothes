<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductControllerTest extends WebTestCase
{
    public function testProduct(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/product/1');
        $form = $crawler->selectButton('form[submit]')->form();
        $form['form[product]'] = '1';
        $crawler = $client->submit($form);
        $this->assertResponseRedirects('/basket');
    }
}
