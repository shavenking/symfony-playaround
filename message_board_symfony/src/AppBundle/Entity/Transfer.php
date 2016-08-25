<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * Transfer
 *
 * @ORM\Table(name="transfers")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TransferRepository")
 */
class Transfer
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
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="transfers")
     */
    private $user;

    /**
     * @var int
     *
     * @ORM\Column(name="amount", type="integer")
     */
    private $amount;

    /**
     * @var int
     *
     * @ORM\Column(name="balance", type="integer")
     */
    private $balance;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="transfered_at", type="datetime")
     */
    private $transferedAt;

    public function __construct($amount = null)
    {
        $this->setAmount($amount);

        // set default to now
        $this->setTransferedAt(new DateTime);
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

    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set amount
     *
     * @param integer $amount
     *
     * @return Transfer
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set balance
     *
     * @param integer $balance
     *
     * @return Transfer
     */
    public function setBalance($balance)
    {
        $this->balance = $balance;

        return $this;
    }

    /**
     * Get balance
     *
     * @return int
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * Set transferedAt
     *
     * @param \DateTime $transferedAt
     *
     * @return Transfer
     */
    public function setTransferedAt($transferedAt)
    {
        $this->transferedAt = $transferedAt;

        return $this;
    }

    /**
     * Get transferedAt
     *
     * @return \DateTime
     */
    public function getTransferedAt()
    {
        return $this->transferedAt;
    }
}

