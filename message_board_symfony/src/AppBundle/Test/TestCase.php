<?php

namespace AppBundle\Test;

use AppBundle\AssertTrait\DatabaseAssertTrait;
use AppBundle\AssertTrait\ResponseAssertTrait;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TestCase extends WebTestCase
{
    use DatabaseAssertTrait, ResponseAssertTrait;

    protected $em;

    protected $client;

    protected function setUp()
    {
        parent::setUp();

        $this->client = $this->createClient();
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
}
