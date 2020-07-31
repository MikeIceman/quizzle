<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Menu
 *
 * @package AppBundle\Entity
 * @ORM\Table(name="menu", indexes={@ORM\Index(name="FK_menu_parent", columns={"parent_id"})})
 * @ORM\Entity
 */
class Menu
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
     * @ORM\Column(name="title", type="string", length=50, nullable=true, options={"comment":"Menu item title"})
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(
     *     name="description",
     *     type="string",
     *     length=50,
     *     nullable=true,
     *     options={"comment":"Menu item description"}
     * )
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(
     *     name="icon",
     *     type="string",
     *     length=50,
     *     nullable=true,
     *     options={"comment":"Icon class from Font Awesome or other vendor package"}
     * )
     */
    private $icon;

    /**
     * @var string
     *
     * @ORM\Column(
     *     name="route",
     *     type="string",
     *     length=100,
     *     nullable=false,
     *     options={"comment":"Menu item route from base path"}
     * )
     */
    private $route;

    /**
     * @var array
     *
     * @ORM\Column(name="roles", type="array", nullable=false, options={"comment":"User roles who can see this item"})
     */
    private $roles;

    /**
     * @var integer
     *
     * @ORM\Column(name="order", type="integer", nullable=false, options={"default":1000, "comment":"Sorting order"})
     */
    private $order = '1000';

    /**
     * @var Menu
     *
     * @ORM\ManyToOne(targetEntity="Menu", inversedBy="childs")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     * })
     */
    private $parent;

    /**
     * @var Menu
     *
     * @ORM\OneToMany(targetEntity="Menu", mappedBy="parent")
     */
    private $childs;

    public function __construct()
    {
        $this->childs = new ArrayCollection();
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
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @param string $icon
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;
    }

    /**
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @param string $route
     */
    public function setRoute($route)
    {
        $this->route = $route;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param int $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * @return Menu
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param Menu $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return Menu
     */
    public function getChilds()
    {
        return $this->childs;
    }

    /**
     * @param Menu $childs
     */
    public function setChilds($childs)
    {
        $this->childs = $childs;
    }
}
