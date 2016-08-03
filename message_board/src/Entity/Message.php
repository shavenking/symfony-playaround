<?php
namespace App\Entity;

/**
 * @Entity @Table(name="messages")
 **/
class Message
{
    /** @Id @Column(type="integer") @GeneratedValue **/
    protected $id;

    /** @Column(type="string", name="display_name") **/
    protected $displayName;

    /** @Column(type="string") **/
    protected $msg;

    public function getId()
    {
        return $this->id;
    }

    public function getDisplayName()
    {
        return $this->displayName;
    }

    public function getMsg()
    {
        return $this->msg;
    }

    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
    }

    public function setMsg($msg)
    {
        $this->msg = $msg;
    }
}
