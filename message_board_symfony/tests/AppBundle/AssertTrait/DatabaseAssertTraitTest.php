<?php

namespace Tests\AppBundle\AssertTrait;

use AppBundle\Entity\Message;
use AppBundle\Test\TestCase;

use Doctrine\ORM\EntityManager;

class DatabaseAssertTraitTest extends TestCase
{
    public function testGetEntityManager()
    {
        $em = $this->getEntityManager();

        $this->assertSame(EntityManager::class, get_class($em));
    }

    /**
     * @depends testGetEntityManager
     */
    public function testAssertExistInDatabase()
    {
        $message = $this->createRandomMessage();

        $this->assertExistInDatabase(
            'AppBundle:Message', [
                'displayName' => $message->getDisplayName(),
                'body' => $message->getBody()
            ]
        );

        try {
            $this->assertExistInDatabase(
                'AppBundle:Message', [
                    'displayName' => 'this is not even a name',
                    'body' => $message->getBody()
                ]
            );
        } catch (\PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @depends testGetEntityManager
     */
    public function testAssertNotExistInDatabase()
    {
        $message = $this->createRandomMessage();

        $this->assertNotExistInDatabase(
            'AppBundle:Message', [
                'displayName' => 'this is not even a name',
                'body' => $message->getBody()
            ]
        );

        try {
            $this->assertNotExistInDatabase(
                'AppBundle:Message', [
                    'displayName' => $message->getDisplayName(),
                    'body' => $message->getBody()
                ]
            );
        } catch (\PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }

        $this->fail();
    }

    protected function createRandomMessage()
    {
        $randomName = 'testing_name_' . rand();
        $randomBody = 'testing_body_' . rand();
        $message = new Message($randomName, $randomBody);

        $this->em->persist($message);
        $this->em->flush();

        return $message;
    }
}
