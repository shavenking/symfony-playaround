<?php

namespace AppBundle\Command;

use AppBundle\Entity\User;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AppUsersCreateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('app:users:create')
            ->setDescription('Create new user.')
            ->addArgument('username', InputArgument::REQUIRED)
            ->addArgument('password', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $user = new User;
        $username = $input->getArgument('username');
        $password = $this->getContainer()
            ->get('security.password_encoder')
            ->encodePassword(
                $user,
                $input->getArgument('password')
            );

        $user->setUsername($username);
        $user->setPassword($password);

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $em->persist($user);
        $em->flush();

        $output->writeln('User created!');
    }

}
