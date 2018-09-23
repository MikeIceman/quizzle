<?php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

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
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=255, nullable=true)
     */
    private $lastname;

    /**
     * @var string
     *
     * @ORM\Column(name="middlename", type="string", length=255, nullable=true)
     */
    private $middlename;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=255, nullable=true)
     */
    private $phone;

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
	public function getMiddlename()
	{
		return $this->middlename;
	}

	/**
	 * @param string $middlename
	 */
	public function setMiddlename($middlename)
	{
		$this->middlename = $middlename;
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

