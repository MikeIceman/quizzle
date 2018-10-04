<?php
	/**
	 * Created by PhpStorm.
	 * User: Iceman
	 * Date: 24.09.2018
	 * Time: 7:13
	 */

	namespace AppBundle\Entity;

	use Doctrine\ORM\Mapping as ORM;

	/**
	 * Balance operations
	 *
	 * @ORM\Table(name="balance_operations", indexes={@ORM\Index(name="FK_user_id", columns={"user_id"})})
	 * @ORM\Entity
	 */
	class Operation
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
		 * @var \DateTime
		 *
		 * @ORM\Column(name="date_added", type="datetime", nullable=false, options={"comment":"Date and time when added"})
		 */
		private $dateAdded;

		/**
		 * @var \DateTime
		 *
		 * @ORM\Column(name="date_updated", type="datetime", nullable=false, options={"comment":"Date and time when updated"})
		 */
		private $dateUpdated;

		/**
		 * @var \User
		 *
		 * @ORM\ManyToOne(targetEntity="User", inversedBy="operations")
		 * @ORM\JoinColumns({
		 *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
		 * })
		 */
		private $user;

		/**
		 * @var \User
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
		 * @ORM\Column(name="operation_type", type="string", nullable=false, columnDefinition="enum('win', 'exchange', 'bonus', 'withdrawal')", options={"default": "win", "comment": "Operation type (win, exchange - bonus balance; deposit, withdrawal - cash balance)"})
		 */
		private $type;

		/**
		 * @var float
		 *
		 * @ORM\Column(name="operation_amount", type="decimal", precision=6, scale=2, nullable=true, options={"default" : 0, "comment": "Operation amount"})
		 */
		private $amount;

		/**
		 * @var string
		 *
		 * @ORM\Column(name="operation_status", type="string", nullable=false, columnDefinition="enum('pending', 'complete', 'reversed')", options={"default": "pending", "comment": "Operation status"})
		 */
		private $status;

		public function __construct() {
			$this->setDateAdded(new \DateTime());
			$this->setDateUpdated(new \DateTime());
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
		 * @return \DateTime
		 */
		public function getDateAdded()
		{
			return $this->dateAdded;
		}

		/**
		 * @param \DateTime $dateAdded
		 */
		public function setDateAdded($dateAdded)
		{
			$this->dateAdded = $dateAdded;
		}

		/**
		 * @return \DateTime
		 */
		public function getDateUpdated()
		{
			return $this->dateUpdated;
		}

		/**
		 * @param \DateTime $dateUpdated
		 */
		public function setDateUpdated($dateUpdated)
		{
			$this->dateUpdated = $dateUpdated;
		}

		/**
		 * @return \User
		 */
		public function getUser()
		{
			return $this->user;
		}

		/**
		 * @param \User $user
		 */
		public function setUser($user)
		{
			$this->user = $user;
		}

		/**
		 * @return \User
		 */
		public function getUpdatedBy()
		{
			return $this->updatedBy;
		}

		/**
		 * @param \User $updatedBy
		 */
		public function setUpdatedBy($updatedBy)
		{
			$this->updatedBy = $updatedBy;
		}

		/**
		 * @return string
		 */
		public function getType()
		{
			return $this->type;
		}

		/**
		 * @param string $type
		 */
		public function setType($type)
		{
			$this->type = $type;
		}

		/**
		 * @return float
		 */
		public function getAmount()
		{
			return $this->amount;
		}

		/**
		 * @param float $amount
		 */
		public function setAmount($amount)
		{
			$this->amount = $amount;
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


	}