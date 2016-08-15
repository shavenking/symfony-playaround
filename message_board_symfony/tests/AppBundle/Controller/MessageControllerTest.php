<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\Message;
use AppBundle\Test\TestCase;

class MessageControllerTest extends TestCase
{
    public function testCreateTopLevelMessage()
    {
        // create message with random data
        $message = $this->createRandomMessage(false);

        $this->client->request('POST', '/messages', [
            'display_name' => $message->getDisplayName(),
            'body' => $message->getBody()
        ]);

        $this->assertStatusOkAndMessageDisplayed($message);
    }

    public function testReplyMessage()
    {
        // create mocked logger
        $logger = $this->client->getContainer()->get('logger');
        $mockedLogger = $this->getMockBuilder(get_class($logger))
            ->disableOriginalConstructor()
            ->getMock();

        // expect logger#info to be called once
        $mockedLogger->expects($this->once())
            ->method('info');

        // replace logger with mocked one
        $this->client->getContainer()->set('logger', $mockedLogger);

        // prepare top level message in database
        $message = $this->createRandomMessage();

        // create reply message with random data
        // and set its parent to the message created above
        $replyMessage = $this->createRandomMessage(false);
        $this->client->request('POST', '/messages', [
            'parent_id' => $message->getId(),
            'display_name' => $replyMessage->getDisplayName(),
            'body' => $replyMessage->getBody()
        ]);

        $this->assertStatusOkAndMessageDisplayed($replyMessage);

        // make sure the parent field is correctly stored
        $this->assertExistInDatabase('AppBundle:Message', [
            'parentId' => $message->getId(),
            'displayName' => $replyMessage->getDisplayName(),
            'body' => $replyMessage->getBody()
        ]);
    }

    public function testUpdateMessage()
    {
        $message = $this->createRandomMessage();

        $updatedName = $message->getDisplayName() . '_updated';
        $updatedBody = $message->getBody() . '_updated';

        $this->client->request('PUT', "/messages/{$message->getId()}", [
            'display_name' => $updatedName,
            'body' => $updatedBody
        ]);

        $this->assertExistInDatabase('AppBundle:Message', [
            'id' => $message->getId(),
            'displayName' => $updatedName,
            'body' => $updatedBody
        ]);
    }

    public function testDeleteMessage()
    {
        $message = $this->createRandomMessage();

        $this->client->request('GET', "/messages/{$message->getId()}/delete");

        $this->assertNotExistInDatabase('AppBundle:Message', [
            'id' => $message->getId()
        ]);
    }

    public function testIndexPageReachable()
    {
        $this->client->request('GET', '/messages');

        $this->assertStatusOk();
    }

    public function testEditPageReachable()
    {
        $message = $this->createRandomMessage();

        $this->client->request('GET', "/messages/{$message->getId()}/edit");

        $this->assertStatusOk();
    }

    public function testReplyPageReachable()
    {
        $message = $this->createRandomMessage();

        $this->client->request('GET', "/messages/{$message->getId()}/reply");

        $this->assertStatusOk();
    }

    protected function createRandomMessage($immediatelyPersist = true)
    {
        // create new message with random data
        $name = 'test_name_' . rand();
        $body = 'test_body_' . rand();
        $message = new Message($name, $body);

        if ($immediatelyPersist) {
            $this->em->persist($message);
            $this->em->flush();
        }

        return $message;
    }

    protected function assertStatusOkAndMessageDisplayed($message)
    {
        $this->assertStatusOk();

        $this->assertContentContains([
            $message->getDisplayName(),
            $message->getBody()
        ]);
    }
}
