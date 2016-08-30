<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Transfer;
use AppBundle\Entity\User;
use AppBundle\Test\TestCase;

use Doctrine\Common\Collections\ArrayCollection;
use ReflectionClass;

class UserTest extends TestCase
{
    public function testIdGetter()
    {
        $ref = new ReflectionClass(User::class);
        $refId = $ref->getProperty('id');
        $refId->setAccessible(true);

        $user = new User;
        $refId->setValue($user, 1);

        $this->assertSame(1, $user->getId());
    }

    public function testAssociationSetters()
    {
        $ref = new ReflectionClass(User::class);
        $refTransfers = $ref->getProperty('transfers');
        $refTransfers->setAccessible(true);

        $user = new User;
        $transfer = new Transfer(rand());

        $user->addTransfer($transfer);

        $this->assertSame($transfer, $refTransfers->getValue($user)->first());
    }

    public function testAssociationGetters()
    {
        $ref = new ReflectionClass(User::class);
        $refTransfers = $ref->getProperty('transfers');
        $refTransfers->setAccessible(true);

        $user = new User;
        $transfer = new Transfer(rand());

        $refTransfers->setValue($user, new ArrayCollection([$transfer]));

        $this->assertSame($transfer, $user->getTransfers()->first());
    }
}
