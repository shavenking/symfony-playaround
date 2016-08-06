<?php

namespace AppBundle\Command;

use AppBundle\Entity\Message;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateMessageCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('app:create-message')
            ->setDescription('Create new message.')
            ->setHelp('This command allows you to create new message.');

        // set arguments
        $args = [
            [
                'displayName',
                InputArgument::REQUIRED,
                'The display name of message author.'
            ],
            [
                'msgBody',
                InputArgument::REQUIRED,
                'The body of message.'
            ]
        ];

        foreach ($args as $arg) {
            call_user_func_array([$this, 'addArgument'], $arg);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // get arguments
        $displayName = $input->getArgument('displayName');
        $msgBody     = $input->getArgument('msgBody');

        // set Message
        $message = new Message;
        $message->setDisplayName($displayName);
        $message->setBody($msgBody);

        // persist Message
        $em = $this->getContainer()->get('doctrine')->getManager();
        $em->persist($message);
        $em->flush();

        // tell user that Message is created
        $output->writeln('Message Created.');
        $output->writeln("Display Name: $displayName");
        $output->writeln("Message Body: $msgBody");
    }
}
