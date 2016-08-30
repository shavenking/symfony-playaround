<?php

namespace Tests\AppBundle\Command;

use AppBundle\Command\AppUsersCreateCommand;
use AppBundle\Entity\User;
use AppBundle\Test\CommandTestCase;

class AppUsersCreateCommandTest extends CommandTestCase
{
    protected $commandClass = AppUsersCreateCommand::class;

    public function testCommandName()
    {
        $commandName = 'app:users:create';

        $command = $this->application->find($commandName);

        $this->assertSame(
            get_class($command),
            $this->commandClass
        );
    }

    public function testCommand()
    {
        $username = 'username_' . rand();
        $password = 'password_' . rand();

        $user = new User;
        $user->setUsername($username);
        $user->setPassword($password);

        // run command
        $this->execute([
            'username' => $user->getUsername(),
            'password' => $user->getPassword()
        ]);

        $encoder = $this->getContainer()
            ->get('security.password_encoder');
        $repository = $this->getContainer()
            ->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:User');

        $user = $repository->findOneBy(['username' => $username]);

        // see in database
        $this->assertNotNull($user);
        // password has to be encrypted
        $this->assertTrue($encoder->isPasswordValid($user, $password));
    }
}
