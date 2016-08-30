<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Transfer;
use AppBundle\Entity\User;
use AppBundle\Test\TestCase;

use Doctrine\Common\Collections\ArrayCollection;
use ReflectionClass;

class TransferTest extends TestCase
{
    public function testUserGetter()
    {
        $ref = new ReflectionClass(Transfer::class);
        $refTransfer = $ref->getProperty('user');
        $refTransfer->setAccessible(true);

        $user = new User;
        $transfer = new Transfer;
        $refTransfer->setValue($transfer, $user);

        $this->assertSame($user, $transfer->getUser());
    }
}
