<?php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Table(name="user", uniqueConstraints={@ORM\UniqueConstraint(name="UNIQ_8D93D64992FC23A8", columns={"username_canonical"}), @ORM\UniqueConstraint(name="UNIQ_8D93D649A0D96FBF", columns={"email_canonical"}), @ORM\UniqueConstraint(name="UNIQ_8D93D649C05FB297", columns={"confirmation_token"})})
 * @ORM\Entity
 */
class User extends BaseUser
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
	protected $id;

	/**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=255, nullable=true)
	 *
	 * @Assert\NotBlank(message="Пожалуйста, введите своё имя", groups={"Registration", "Profile"})
	 * @Assert\Length(
	 *     min=3,
	 *     max=255,
	 *     minMessage="Слишком короткое имя",
	 *     maxMessage="Слишком длинное имя",
	 *     groups={"Registration", "Profile"}
	 * )
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=255, nullable=true)
     *
     * @Assert\NotBlank(message="Пожалуйста, введите свою фамилию", groups={"Registration", "Profile"})
     * @Assert\Length(
     *     min=3,
     *     max=255,
     *     minMessage="Слишком короткая фвмилия",
     *     maxMessage="Слишком длинная фамилия",
     *     groups={"Registration", "Profile"}
     * )
     */
    private $lastname;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=255, nullable=true)
     *
     * @Assert\NotBlank(message="Пожалуйста, введите свой телефон", groups={"Registration", "Profile"})
     * @Assert\Length(
     *     min=10,
     *     max=14,
     *     minMessage="Слишком короткое значение",
     *     maxMessage="Слишком длинное значение",
     *     groups={"Registration", "Profile"}
     * )
     */
    private $phone;

	public function __construct()
	{
		parent::__construct();
		// your own logic
		$this->roles = array('ROLE_USER');
	}

	/**
	 * @return string
	 */
	public function getFirstname()
	{
		return $this->firstname;
	}

	/**
	 * @param string $firstname
	 */
	public function setFirstname($firstname)
	{
		$this->firstname = $firstname;
	}

	/**
	 * @return string
	 */
	public function getLastname()
	{
		return $this->lastname;
	}

	/**
	 * @param string $lastname
	 */
	public function setLastname($lastname)
	{
		$this->lastname = $lastname;
	}

	/**
	 * @return string
	 */
	public function getPhone()
	{
		return $this->phone;
	}

	/**
	 * @param string $phone
	 */
	public function setPhone($phone)
	{
		$this->phone = $phone;
	}

	/**
	 * @return bool|mixed
	 */
	public function getHighestRole()
	{
		$rolesSortedByImportance = ['ROLE_SUPER_ADMIN', 'ROLE_ADMIN', 'ROLE_USER'];
		foreach ($rolesSortedByImportance as $role)
		{
			if (in_array($role, $this->roles))
			{
				return $role;
			}
		}

		return false; // Unknown role?
	}

	/**
	 * @param string $role
	 * @return string
	 */
	public function getRoleDescription($role)
	{
		$roles = ['ROLE_SUPER_ADMIN' => 'Разработчик', 'ROLE_ADMIN' => 'Администратор', 'ROLE_USER' => 'Участник'];
		if(array_key_exists($role, $roles))
		{
			return $roles[$role];
		}

		return ''; // Unknown role?
	}
}

