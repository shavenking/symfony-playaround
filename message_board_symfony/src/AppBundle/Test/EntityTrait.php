<?php

namespace AppBundle\Test;

use AppBundle\Entity\Message;
use AppBundle\Entity\Tag;

trait EntityTrait
{
    protected function createRandomMessage($persist = true)
    {
        $randomName = 'testing_name_' . rand();
        $randomBody = 'testing_body_' . rand();
        $message = new Message($randomName, $randomBody);

        if ($persist) {
            $this->em->persist($message);
            $this->em->flush();
        }

        return $message;
    }

    protected function createRandomTag($persist = true)
    {
        $randomName = 'testing_name_' . rand();

        $tag = new Tag;
        $tag->setName($randomName);

        if ($persist) {
            $this->em->persist($tag);
            $this->em->flush();
        }

        return $tag;
    }
}
