<?php

namespace AppBundle\Test;

use AppBundle\DataFixtures\ORM\LoadUserData;

use Liip\FunctionalTestBundle\Test\WebTestCase;

class TestCase extends WebTestCase
{
    use DatabaseAssertTrait, ResponseAssertTrait, EntityTrait;

    protected $em;

    protected $client;

    protected function setUp()
    {
        parent::setUp();

        $this->loadFixtures([LoadUserData::class]);

        $this->client = static::makeClient($this->getCredentials());
        $this->em = $this->client
            ->getContainer()
            ->get('doctrine.orm.entity_manager');

        // client defaults
        $this->client->followRedirects();
    }

    protected function tearDown()
    {
        parent::tearDown();

        $this->client = null;
        $this->em = null;
    }

    protected function getCredentials()
    {
        return ['username' => 'hugh', 'password' => 'hugh'];
    }
}
