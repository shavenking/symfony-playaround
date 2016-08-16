<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\Message;
use AppBundle\Entity\Tag;
use AppBundle\Event\MessageRepliedEvent;

use Psr\Log\LoggerInterface;

class MessageRepliedListener
{
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function onMessageReplied(MessageRepliedEvent $event)
    {
        $parent = $event->getParent();
        $child = $event->getChild();

        $this->logger->info(
            "{$parent->getId()} is replied by {$child->getId()}"
        );
    }
}
