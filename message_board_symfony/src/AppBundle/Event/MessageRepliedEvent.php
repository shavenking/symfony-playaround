<?php

namespace AppBundle\Event;

use AppBundle\Entity\Message;

use Symfony\Component\EventDispatcher\Event;

class MessageRepliedEvent extends Event
{
    protected $parent;

    protected $child;

    public function __construct(Message $parent, Message $child)
    {
        $this->setParent($parent);
        $this->setChild($child);
    }

    public function setParent(Message $message)
    {
        $this->parent = $message;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function setChild(Message $message)
    {
        $this->child = $message;
    }

    public function getChild()
    {
        return $this->child;
    }
}
