<?php

namespace Tests\AppBundle\Controller;

use AppBundle\DataFixtures\ORM\LoadUserData;

use Liip\FunctionalTestBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    protected function setUp()
    {
        $this->loadFixtures([LoadUserData::class]);
    }

    public function testIndex()
    {
        $client = static::makeClient($this->getCredentials());

        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Welcome to Symfony', $crawler->filter('#container h1')->text());
    }

    protected function getCredentials()
    {
        return ['username' => 'hugh', 'password' => 'hugh'];
    }
}
