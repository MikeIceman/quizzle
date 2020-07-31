<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Wined prizes
 *
 * @package AppBundle\Entity
 * @ORM\Table(name="user_prizes", indexes={@ORM\Index(name="FK_prize_id", columns={"prize_id"})})
 * @ORM\Entity
 */
class UserPrize
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="prizes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="date_added", type="datetime", nullable=false, options={"comment":"Date and time when added"})
     */
    private $dateAdded;

    /**
     * @var DateTime
     *
     * @ORM\Column(
     *     name="date_updated",
     *     type="datetime",
     *     nullable=false,
     *     options={"comment":"Date and time when updated"}
     * )
     */
    private $dateUpdated;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="updated_by", referencedColumnName="id")
     * })
     */
    private $updatedBy;

    /**
     * @var string
     *
     * @ORM\Column(
     *     name="status",
     *     type="string",
     *     nullable=false,
     *     columnDefinition="enum('pending', 'sent', 'received', 'rejected')",
     *     options={"default": "pending", "comment":"Current delivery status"}
     * )
     */
    private $status;

    /**
     * @var Prize
     *
     * @ORM\ManyToOne(targetEntity="Prize")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="prize_id", referencedColumnName="id")
     * })
     */
    private $prize;

    /**
     * @var WinWheelSpin
     *
     * @ORM\ManyToOne(targetEntity="WinWheelSpin")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="spin_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $spin;


    public function __construct()
    {
        $this->setDateAdded(new DateTime());
        $this->setDateUpdated(new DateTime());
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return DateTime
     */
    public function getDateAdded()
    {
        return $this->dateAdded;
    }

    /**
     * @param DateTime $dateAdded
     */
    public function setDateAdded($dateAdded)
    {
        $this->dateAdded = $dateAdded;
    }

    /**
     * @return DateTime
     */
    public function getDateUpdated()
    {
        return $this->dateUpdated;
    }

    /**
     * @param DateTime $dateUpdated
     */
    public function setDateUpdated($dateUpdated)
    {
        $this->dateUpdated = $dateUpdated;
    }

    /**
     * @return User
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    /**
     * @param User $updatedBy
     */
    public function setUpdatedBy($updatedBy)
    {
        $this->updatedBy = $updatedBy;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return Prize
     */
    public function getPrize()
    {
        return $this->prize;
    }

    /**
     * @param Prize $prize
     */
    public function setPrize($prize)
    {
        $this->prize = $prize;
    }

    /**
     * @return WinWheelSpin
     */
    public function getSpin()
    {
        return $this->spin;
    }

    /**
     * @param WinWheelSpin $spin
     */
    public function setSpin($spin)
    {
        $this->spin = $spin;
    }
}
