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
        $tag = $this->createRandomTag(false);

        // execute command
        $this->mockHelper(QuestionHelper::class)->answers(
            $message->getId(),
            $tag->getName()
        )->execute();

        // see new tag in database
        $tag = $this->em->getRepository('AppBundle:Tag')->findOneBy([
            'name' => $tag->getName()
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
