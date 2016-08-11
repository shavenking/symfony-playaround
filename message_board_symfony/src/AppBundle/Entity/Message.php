<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Message
 *
 * @ORM\Table(name="messages")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MessageRepository")
 */
class Message
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="display_name", type="string", length=40)
     */
    private $displayName;

    /**
     * @var string
     *
     * @ORM\Column(name="body", type="string", length=255)
     */
    private $body;

    /**
     * @ORM\Column(
     *     name="parent_id",
     *     type="integer",
     *     nullable=true
     * )
     */
    private $parentId;

    /**
     * @ORM\OneToMany(
     *     targetEntity="Message",
     *     mappedBy="parent",
     *     cascade={"remove"}
     * )
     */
    private $children;

    /**
     * @ORM\ManyToOne(targetEntity="Message", inversedBy="children")
     */
    private $parent;

    /**
     * @ORM\ManyToMany(targetEntity="Tag", inversedBy="messages")
     */
    private $tags;

    public function __construct()
    {
        $this->replies = new ArrayCollection;
        $this->tags    = new ArrayCollection;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set displayName
     *
     * @param string $displayName
     *
     * @return Message
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;

        return $this;
    }

    /**
     * Get displayName
     *
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * Set body
     *
     * @param string $body
     *
     * @return Message
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    public function setParent(Message $message)
    {
        $this->parent = $message;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function addChildren(Message $message)
    {
        $this->children->add($message);
    }

    public function getChildren()
    {
        return $this->children;
    }

    public function addTag(Tag $tag)
    {
        $this->tags->add($tag);
    }

    public function getTags()
    {
        return $this->tags;
    }
}

