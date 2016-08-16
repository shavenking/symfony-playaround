<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Message;
use AppBundle\Entity\Tag;
use AppBundle\Test\TestCase;

use Doctrine\Common\Collections\ArrayCollection;
use ReflectionClass;

class MessageTest extends TestCase
{
    public function testConstructor()
    {
        $name = 'test_name_' . rand();
        $body = 'test_body_' . rand();
        $message = new Message($name, $body);

        $ref = new ReflectionClass(Message::class);
        $refName = $ref->getProperty('displayName');
        $refBody = $ref->getProperty('body');

        $refName->setAccessible(true);
        $refBody->setAccessible(true);

        $this->assertSame($name, $refName->getValue($message));
        $this->assertSame($body, $refBody->getValue($message));
    }

    public function testFieldSetters()
    {
        $name = 'test_name_' . rand();
        $body = 'test_body_' . rand();
        $message = new Message;

        $message->setDisplayName($name);
        $message->setBody($body);

        $ref = new ReflectionClass(Message::class);
        $refName = $ref->getProperty('displayName');
        $refBody = $ref->getProperty('body');

        $refName->setAccessible(true);
        $refBody->setAccessible(true);

        $this->assertSame($name, $refName->getValue($message));
        $this->assertSame($body, $refBody->getValue($message));
    }

    public function testFieldGetters()
    {
        $ref = new ReflectionClass(Message::class);
        $refId = $ref->getProperty('id');
        $refName = $ref->getProperty('displayName');
        $refBody = $ref->getProperty('body');

        $refId->setAccessible(true);
        $refName->setAccessible(true);
        $refBody->setAccessible(true);

        $randId = 'test_id_' . rand();
        $randName = 'test_name_' . rand();
        $randBody = 'test_body_' . rand();
        $message = new Message;

        $refId->setValue($message, $randId);
        $refName->setValue($message, $randName);
        $refBody->setValue($message, $randBody);

        $this->assertSame($randId, $message->getId());
        $this->assertSame($randName, $message->getDisplayName());
        $this->assertSame($randBody, $message->getBody());
    }

    public function testAssociationSetters()
    {
        $ref = new ReflectionClass(Message::class);
        $refParent = $ref->getProperty('parent');
        $refChildren = $ref->getProperty('children');
        $refTags = $ref->getProperty('tags');

        $refParent->setAccessible(true);
        $refChildren->setAccessible(true);
        $refTags->setAccessible(true);

        $parent = new Message(
            'test_name_' . rand(),
            'test_body_' . rand()
        );

        $child = new Message(
            'test_name_' . rand(),
            'test_body_' . rand()
        );

        $child->setParent($parent);
        $parent->addChildren($child);

        $this->assertSame($parent, $refParent->getValue($child));
        $this->assertSame($child, $refChildren->getValue($parent)->first());

        $tag = new Tag;
        $child->addTag($tag);

        $this->assertSame($tag, $refTags->getValue($child)->first());
    }

    public function testAssociationGetters()
    {
        $ref = new ReflectionClass(Message::class);
        $refParent = $ref->getProperty('parent');
        $refChildren = $ref->getProperty('children');
        $refTags = $ref->getProperty('tags');

        $refParent->setAccessible(true);
        $refChildren->setAccessible(true);
        $refTags->setAccessible(true);

        $parent = new Message(
            'test_name_' . rand(),
            'test_body_' . rand()
        );

        $child = new Message(
            'test_name_' . rand(),
            'test_body_' . rand()
        );

        // set parent
        $refParent->setValue($child, $parent);
        // add children
        $refChildren->setValue($parent, new ArrayCollection([$child]));

        $this->assertSame($parent, $child->getParent());
        $this->assertSame($child, $parent->getChildren()->first());

        $tag = new Tag;
        $refTags->setValue($child, new ArrayCollection([$tag]));

        $this->assertSame($tag, $child->getTags()->first());
    }
}
