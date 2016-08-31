<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CartControllerTest extends WebTestCase
{
    public function testShowWhenCartIsEmpty()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/cart/show');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('.empty-text-wrap')->count());
    }
}
