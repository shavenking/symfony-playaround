<?php

namespace Tests\AppBundle\Command;

use AppBundle\Command\CreateMessageCommand;
use AppBundle\Entity\Message;
use AppBundle\Test\CommandTestCase;

class CreateMessageCommandTest extends CommandTestCase
{
    protected $commandClass = CreateMessageCommand::class;

    public function testCommandName()
    {
        $commandName = 'app:create-message';

        $command = $this->application->find($commandName);

        $this->assertSame(
            get_class($command),
            $this->commandClass
        );
    }

    public function testCreateMessage()
    {
        $message = $this->createRandomMessage(false);

        // run command
        $this->execute([
            'displayName' => $message->getDisplayName(),
            'msgBody' => $message->getBody()
        ]);

        // see in database
        $this->assertExistInDatabase('AppBundle:Message', [
            'displayName' => $message->getDisplayName(),
            'body' => $message->getBody()
        ]);
    }
}
