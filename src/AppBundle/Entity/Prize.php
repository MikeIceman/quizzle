<?php
	/**
	 * Created by PhpStorm.
	 * User: Iceman
	 * Date: 24.09.2018
	 * Time: 7:49
	 */

	namespace AppBundle\Entity;

	use Doctrine\ORM\Mapping as ORM;

	/**
	 * Available prizes
	 *
	 * @ORM\Table(name="prizes")
	 * @ORM\Entity
	 */
	class Prize
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
		 * @ORM\Column(name="title", type="string", length=50, nullable=true, options={"comment":"Item title"})
		 */
		private $title;

		/**
		 * @var string
		 *
		 * @ORM\Column(name="desctiption", type="string", length=250, nullable=true, options={"comment":"Item title"})
		 */
		private $desctiption;

		/**
		 * @var string
		 *
		 * @ORM\Column(name="image", type="string", length=50, nullable=true, options={"comment":"Item image"})
		 */
		private $image;

		/**
		 * @var decimal
		 *
		 * @ORM\Column(name="cost", type="decimal", precision=6, scale=2, nullable=true, options={"default" : 0, "comment": "Item cost"})
		 */
		private $cost;

		/**
		 * @var integer
		 *
		 * @ORM\Column(name="quantity", type="decimal", precision=6, scale=2, nullable=true, options={"default" : 0, "comment": "Available quantity"})
		 */
		private $quantity;

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
		public function getDesctiption()
		{
			return $this->desctiption;
		}

		/**
		 * @param string $desctiption
		 */
		public function setDesctiption($desctiption)
		{
			$this->desctiption = $desctiption;
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
		 * @return decimal
		 */
		public function getCost()
		{
			return $this->cost;
		}

		/**
		 * @param decimal $cost
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


	}