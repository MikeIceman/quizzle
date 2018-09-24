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
		 * @var \User
		 *
		 * @ORM\ManyToOne(targetEntity="User", inversedBy="operations")
		 * @ORM\JoinColumns({
		 *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
		 * })
		 */
		private $user;

		/**
		 * @var string
		 *
		 * @ORM\Column(name="operation_type", type="string", nullable=false, columnDefinition="enum('win', 'exchange', 'deposit', 'withdrawal')", options={"default": "win", "comment": "Operation type (win, exchange - bonus balance; deposit, withdrawal - cash balance)"})
		 */
		private $type;

		/**
		 * @var decimal
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
		 * @return decimal
		 */
		public function getAmount()
		{
			return $this->amount;
		}

		/**
		 * @param decimal $amount
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