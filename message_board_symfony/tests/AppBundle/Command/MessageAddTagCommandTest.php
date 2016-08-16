<?php

namespace Tests\AppBundle\Command;

use AppBundle\Command\MessagesAddTagCommand;
use AppBundle\Entity\Message;
use AppBundle\Entity\Tag;
use AppBundle\Test\CommandTestCase;

use Sensio\Bundle\GeneratorBundle\Command\Helper\QuestionHelper;

class MessageAddTagCommandTest extends CommandTestCase
{
    protected $commandClass = MessagesAddTagCommand::class;

    public function testCommandName()
    {
        $commandName = 'app:messages:add-tag';

        $command = $this->application->find($commandName);

        $this->assertSame(
            get_class($command),
            MessagesAddTagCommand::class
        );
    }

    public function testAddNewTag()
    {
        $message = $this->createRandomMessage();
        $randomTagName = 'test_tag_name_' . rand();

        // execute command
        $this->mockHelper(QuestionHelper::class)->answers(
            $message->getId(),
            $randomTagName
        )->execute();

        // see new tag in database
        $tag = $this->em->getRepository('AppBundle:Tag')->findOneBy([
            'name' => $randomTagName
        ]);
        $this->assertNotNull($tag, 'Tag is not created.');

        $this->assertMessageTagAssociated($message, $tag);
    }

    public function testAddExistingTag()
    {
        // create message and tag
        $message = $this->createRandomMessage();
        $tag = $this->createRandomTag();

        // execute command
        $this->mockHelper(QuestionHelper::class)->answers(
            $message->getId(),
            $tag->getId()
        )->execute();

        $this->assertMessageTagAssociated($message, $tag);
    }

    protected function createRandomMessage()
    {
        $randomName = 'test_name_' . rand();
        $randomBody = 'test_body_' . rand();

        $message = new Message($randomName, $randomBody);

        $this->em->persist($message);
        $this->em->flush();

        return $message;
    }

    protected function createRandomTag()
    {
        $randomName = 'test_name_' . rand();

        $tag = new Tag();
        $tag->setName($randomName);

        $this->em->persist($tag);
        $this->em->flush();

        return $tag;
    }

    /**
     * Verify association between Message and Tag.
     */
    protected function assertMessageTagAssociated($message, $tag)
    {
        $qb = $this->em->createQueryBuilder();

        $query = $qb->select($qb->expr()->count('m'))
            ->from('AppBundle:Message', 'm')
            ->leftJoin('m.tags', 't')
            ->where($qb->expr()->eq('m.id', ':m'))
            ->andWhere($qb->expr()->eq('t.id', ':t'))
            ->setParameter('m', $message)
            ->setParameter('t', $tag)
            ->getQuery();

        $result = $query->getSingleScalarResult();

        $this->assertEquals(1, $result, 'Message and Tag are not associated.');
    }
}
