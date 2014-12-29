<?php

namespace Shoppinglist\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * ShoppinglistItem
 *
 * @ORM\Table(name="shoppinglist_item", indexes={@ORM\Index(name="id_shoppinglist", columns={"fk_shoppinglist"})})
 * @ORM\Entity(repositoryClass="Shoppinglist\ApiBundle\Entity\ShoppinglistItemRepository")
 */
class ShoppinglistItem
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_shoppinglist_item", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $idShoppinglistItem;

    /**
     * @var string
     *
     * @ORM\Column(name="product_name", type="string", length=64, nullable=false)
     */
    private $productName;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="Please choose unit")
     * @ORM\Column(name="unit", type="string", length=45, nullable=false)
     */
    private $unit;

    /**
     * @var float
     *
     * @Assert\NotBlank(message="Please choose quantity")
     * @Assert\GreaterThan(value=0, message="Please enter valid quantity")
     * @ORM\Column(name="quantity", type="float", precision=5, scale=2, nullable=false)
     */
    private $quantity;

    /**
     * @var integer
     *
     * @ORM\Column(name="picked", type="integer", nullable=false)
     */
    private $picked;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var \Shoppinglist\ApiBundle\Entity\Shoppinglist
     * 
     * @Assert\NotBlank(message="Please choose shopping")
     * @Assert\GreaterThan(value=0, message="Invalid data")
     * @ORM\ManyToOne(targetEntity="Shoppinglist\ApiBundle\Entity\Shoppinglist")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fk_shoppinglist", referencedColumnName="id_shoppinglist")
     * })
     */
    private $fkShoppinglist;

    /**
     * @var string
     *
     * @ORM\Column(name="notes", type="string", length=255)
     */
    private $notes;
    
    /**
     * @var \Shoppinglist\ApiBundle\Entity\User
     * 
     * @Assert\NotBlank(message="Please choose user")
     * @Assert\GreaterThan(value=0, message="Invalid user")
     * @ORM\ManyToOne(targetEntity="Shoppinglist\ApiBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fk_user", referencedColumnName="id_user")
     * })
     */
    private $fkUser;


    /**
     * Set fkUser
     *
     * @param integer $fkUser
     * @return ShoppinglistItem
     */
    public function setFkUser($fkUser)
    {
        $this->fkUser = $fkUser;

        return $this;
    }

    /**
     * Get fkUser
     *
     * @return \Shoppinglist\ApiBundle\Entity\User
     */
    public function getFkUser()
    {
        return $this->fkUser;
    }
    
    /**
     * Get idShoppinglistItem
     *
     * @return integer 
     */
    public function getIdShoppinglistItem()
    {
        return $this->idShoppinglistItem;
    }

    /**
     * Set productName
     *
     * @param string $productName
     * @return ShoppinglistItem
     */
    public function setProductName($productName)
    {
        $this->productName = $productName;

        return $this;
    }

    /**
     * Get productName
     *
     * @return string 
     */
    public function getProductName()
    {
        return $this->productName;
    }

    /**
     * Set unit
     *
     * @param string $unit
     * @return ShoppinglistItem
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;

        return $this;
    }

    /**
     * Get unit
     *
     * @return string 
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * Set quantity
     *
     * @param float $quantity
     * @return ShoppinglistItem
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return float 
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set picked
     *
     * @param boolean $picked
     * @return ShoppinglistItem
     */
    public function setPicked($picked)
    {
        $this->picked = $picked;

        return $this;
    }

    /**
     * Get picked
     *
     * @return boolean 
     */
    public function getPicked()
    {
        return $this->picked;
    }

    /**
     * Set createdAt
     *
     * @return ShoppinglistItem
     */
    public function setCreatedAt()
    {
        if(!$this->getCreatedAt()) {
            $this->createdAt = new \DateTime();
        }

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set fkShoppinglist
     *
     * @param \Shoppinglist\ApiBundle\Entity\Shoppinglist $fkShoppinglist
     * @return ShoppinglistItem
     */
    public function setFkShoppinglist(\Shoppinglist\ApiBundle\Entity\Shoppinglist $fkShoppinglist = null)
    {
        $this->fkShoppinglist = $fkShoppinglist;

        return $this;
    }

    /**
     * Get fkShoppinglist
     *
     * @return \Shoppinglist\ApiBundle\Entity\Shoppinglist 
     */
    public function getFkShoppinglist()
    {
        return $this->fkShoppinglist;
    }
    
    /**
     * Set notes
     *
     * @param string $notes
     * @return ShoppinglistItem
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * Get notes
     *
     * @return string 
     */
    public function getNotes()
    {
        return $this->notes;
    }
}
