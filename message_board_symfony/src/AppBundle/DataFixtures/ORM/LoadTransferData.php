<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Transfer;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadTransferData extends AbstractFixture implements DependentFixtureInterface
{
    public function getDependencies()
    {
        return [LoadUserData::class];
    }

    public function load(ObjectManager $manager)
    {
        $user = $this->getReference('user');
        $user = $manager->merge($user);

        $manager->transactional(function ($manager) use ($user) {
            $balance = $user->getBalance();

            foreach (range(0, 20) as $i) {
                $amount = rand(-999, 999);
                $balance += $amount;
                $transfer = new Transfer($amount);
                $transfer->setUser($user);

                $manager->persist($transfer);
            }

            $user->setBalance($balance);

            $manager->flush();
        });
    }
}
