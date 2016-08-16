<?php

namespace AppBundle\Command;

use AppBundle\Entity\Tag;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class MessagesAddTagCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('app:messages:add-tag')
            ->setDescription('Add Tag to Message.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $em = $this->getContainer()->get('doctrine')->getManager();

        // get Question
        $messages = $em->getRepository('AppBundle:Message')->findAll();
        $messageQuestion = new Question(
            $this->getMessageQuestionStr($messages)
        );

        $tags = $em->getRepository('AppBundle:Tag')->findAll();
        $tagQuestion = new Question(
            $this->getTagQuestionStr($tags)
        );

        // ask Question
        $messageId = $helper->ask($input, $output, $messageQuestion);
        $tagIdOrNewTagName = $helper->ask($input, $output, $tagQuestion);

        // try to get entity from user input
        $message = $em->getRepository('AppBundle:Message')->find($messageId);
        $tag = $em->getRepository('AppBundle:Tag')->find($tagIdOrNewTagName);

        // create Tag if not exists
        if (is_null($tag)) {
            $tag = new Tag;
            $tag->setName($tagIdOrNewTagName);

            $em->persist($tag);
        }

        // finish command
        $message->addTag($tag);
        $em->flush();

        // show Tag that are added to Message
        $output->writeln('');
        $output->writeln('The Message now has Tag:');
        foreach ($message->getTags() as $tag) {
            $output->writeln('- ' . $tag->getName());
        }
    }

    protected function getMessageQuestionStr($messages)
    {
        $questionStr = "Which Message are you going to add Tag on?\n\n";
        foreach ($messages as $message) {
            $messageId = $message->getId();
            $displayName = $message->getDisplayName();
            $body = $message->getBody();

            $questionStr .= "[$messageId] $displayName: $body\n";
        }
        $questionStr .= "\n> ";

        return $questionStr;
    }

    protected function getTagQuestionStr($tags)
    {
        $questionStr = "What Tag are you going to add on Message (or just type a new tag name)?\n\n";
        foreach ($tags as $tag) {
            $tagId = $tag->getId();
            $tagName = $tag->getName();

            $questionStr .= "[$tagId] $tagName\n";
        }
        $questionStr .= "\n> ";

        return $questionStr;
    }
}
