<?php
	/**
	 * Created by PhpStorm.
	 * User: Iceman
	 * Date: 24.09.2018
	 * Time: 6:13
	 */

	namespace AppBundle\Entity;

	use Doctrine\ORM\Mapping as ORM;

	/**
	 * WinWheel Spins
	 *
	 * @ORM\Table(name="winwheel_spins", indexes={@ORM\Index(name="FK_user_id", columns={"user_id"})})
	 * @ORM\Entity
	 */
	class WinWheelSpin
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
		 * @ORM\ManyToOne(targetEntity="User", inversedBy="spins")
		 * @ORM\JoinColumns({
		 *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
		 * })
		 */
		private $user;

		/**
		 * @var \DateTime
		 *
		 * @ORM\Column(name="date_spinned", type="datetime", nullable=false, options={"comment":"Date and time when spinned"})
		 */
		private $dateSpinned;

		/**
		 * @var string
		 *
		 * @ORM\Column(name="prize_type", type="string", nullable=false, columnDefinition="enum('cash', 'bonus', 'prize', 'nothing')", options={"default": "nothing", "comment":"Winned prize type"})
		 */
		private $prizeType;

		/**
		 * @var decimal
		 *
		 * @ORM\Column(name="prize_amount", type="decimal", precision=6, scale=2, nullable=true, options={"default" : 0, "comment":"Bonus or cash amount, prize cost if item winned"})
		 */
		private $prizeAmount;


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
		 * @return \DateTime
		 */
		public function getDateSpinned()
		{
			return $this->dateSpinned;
		}

		/**
		 * @param \DateTime $dateSpinned
		 */
		public function setDateSpinned($dateSpinned)
		{
			$this->dateSpinned = $dateSpinned;
		}

		/**
		 * @return string
		 */
		public function getPrizeType()
		{
			return $this->prizeType;
		}

		/**
		 * @param string $prizeType
		 */
		public function setPrizeType($prizeType)
		{
			$this->prizeType = $prizeType;
		}

		/**
		 * @return string
		 */
		public function getPrizeAmount()
		{
			return $this->prizeAmount;
		}

		/**
		 * @param string $prizeAmount
		 */
		public function setPrizeAmount($prizeAmount)
		{
			$this->prizeAmount = $prizeAmount;
		}


	}