<?php
	/**
	 * Created by PhpStorm.
	 * User: Iceman
	 * Date: 24.09.2018
	 * Time: 7:49
	 */

	namespace AppBundle\Entity;

	use Doctrine\ORM\Mapping as ORM;
	use JsonSerializable;

	/**
	 * Available prizes
	 *
	 * @ORM\Table(name="prizes")
	 * @ORM\Entity
	 */
	class Prize implements JsonSerializable
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
		 * @var string
		 *
		 * @ORM\Column(name="title", type="string", length=50, nullable=true, options={"comment": "Item title"})
		 */
		private $title;

		/**
		 * @var string
		 *
		 * @ORM\Column(name="description", type="text", nullable=true, options={"comment": "Item desctiption"})
		 */
		private $description;

		/**
		 * @var string
		 *
		 * @ORM\Column(name="image", type="string", length=50, nullable=true, options={"comment": "Item image"})
		 */
		private $image;

		/**
		 * @var float
		 *
		 * @ORM\Column(name="cost", type="decimal", precision=6, scale=2, nullable=true, options={"default" : 0, "comment": "Item cost in $"})
		 */
		private $cost;

		/**
		 * @var integer
		 *
		 * @ORM\Column(name="quantity", type="decimal", precision=6, scale=2, nullable=true, options={"default" : 0, "comment": "Available quantity"})
		 */
		private $quantity;

		/**
		 * @var boolean
		 *
		 * @ORM\Column(name="is_active", type="boolean", nullable=false, options={"default" : true, "comment": "Activity"})
		 */
		private $isActive;

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
		 * @return string
		 */
		public function getTitle()
		{
			return $this->title;
		}

		/**
		 * @param string $title
		 */
		public function setTitle($title)
		{
			$this->title = $title;
		}

		/**
		 * @return string
		 */
		public function getDescription()
		{
			return $this->description;
		}

		/**
		 * @param string $description
		 */
		public function setDescription($description)
		{
			$this->desctiption = $description;
		}

		/**
		 * @return string
		 */
		public function getImage()
		{
			return $this->image;
		}

		/**
		 * @param string $image
		 */
		public function setImage($image)
		{
			$this->image = $image;
		}

		/**
		 * @return float
		 */
		public function getCost()
		{
			return $this->cost;
		}

		/**
		 * @param float $cost
		 */
		public function setCost($cost)
		{
			$this->cost = $cost;
		}

		/**
		 * @return int
		 */
		public function getQuantity()
		{
			return $this->quantity;
		}

		/**
		 * @param int $quantity
		 */
		public function setQuantity($quantity)
		{
			$this->quantity = $quantity;
		}

		/**
		 * @param int $quantity
		 */
		public function updateQuantity($quantity)
		{
			$this->quantity += $quantity;
		}

		/**
		 * @return bool
		 */
		public function isActive()
		{
			return $this->isActive;
		}

		/**
		 * @param bool $isActive
		 */
		public function setIsActive($isActive)
		{
			$this->isActive = $isActive;
		}

		public function jsonSerialize()
		{
			return array(
				'id' => $this->id,
				'title' => $this->title,
				'description' => $this->desctiption,
				'image' => $this->image,
				'cost' => $this->cost,
				'quantity' => $this->quantity,
				'is_active' => $this->isActive
			);
		}

	}