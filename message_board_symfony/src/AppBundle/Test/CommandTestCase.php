<?php

namespace AppBundle\Test;

use AppBundle\Test\FluentCommandTrait;

use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class CommandTestCase extends TestCase
{
    use FluentCommandTrait;

    protected $application;

    protected $command;

    protected function setUp()
    {
        parent::setUp();

        $kernel = $this->client->getKernel();
        $command = $this->instantiateCommand($this->commandClass);

        $this->command = $command;
        $this->application = new Application($kernel);
        $this->application->add($command);
    }

    protected function tearDown()
    {
        parent::tearDown();

        $this->application = null;
    }

    protected function instantiateCommand($commandClass)
    {
        $ref = new ReflectionClass($commandClass);
        $command = $ref->newInstanceArgs([]);

        return $command;
    }
}
