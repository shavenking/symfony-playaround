<?php

namespace AppBundle\Test;

use Symfony\Component\Console\Tester\CommandTester;

trait FluentCommandTrait
{
    protected $mockingHelper;

    protected function execute(array $input = [], array $options = [])
    {
        $commandTester = new CommandTester($this->command);
        $commandTester->execute($input, $options);

        return $commandTester;
    }

    protected function mockHelper($helperClassName)
    {
        $this->mockingHelper = $this->getMockBuilder($helperClassName)
            ->disableOriginalConstructor()
            ->setMethods(['ask'])
            ->getMock();

        return $this;
    }

    protected function answers()
    {
        $answers = func_get_args();

        $this->mockingHelper->method('ask')->will(
            call_user_func_array([$this, 'onConsecutiveCalls'], $answers)
        );

        // set helper
        $helper = $this->mockingHelper;
        $this->command->getHelperSet()->set($helper, $helper->getName());

        return $this;
    }
}
