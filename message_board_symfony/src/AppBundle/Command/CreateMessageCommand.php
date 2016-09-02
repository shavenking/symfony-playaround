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
        $msgBody = $input->getArgument('msgBody');
        $em = $this->getContainer()->get('doctrine')->getManager();
        $batch = 1000;
        $flushTimeCollection = [];

        $time = -microtime(true);
        foreach (range(1, 100000) as $i) {
            // set Message
            $message = new Message;
            $message->setDisplayName($displayName);
            $message->setBody($msgBody);

            // persist Message
            $em->persist($message);

            if (!($i % $batch)) {
                $flushTime = -microtime(true);
                $em->flush();
                $flushTimeCollection[] = ($flushTime += microtime(true));
                $em->clear();
            }
        }
        $flushTime = -microtime(true);
        $em->flush();
        $flushTimeCollection[] = ($flushTime += microtime(true));
        $output->writeln('Memory: ' . memory_get_usage(true) / (1024 * 1024) . ' M');
        $output->writeln('Total Time: ' . $time += microtime(true));
        $output->writeln('Avg Flush Time: ' . array_sum($flushTimeCollection) / count($flushTimeCollection));
    }
}
