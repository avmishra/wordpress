<?php

namespace Shoppinglist\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ShoppinglistUser
 *
 * @ORM\Table(name="shoppinglist_user", indexes={@ORM\Index(name="fk_shoppinglist_user_1_idx", columns={"fk_shoppinglist"}), @ORM\Index(name="fk_user_1_idx", columns={"fk_user"})})
 * @ORM\Entity
 */
class ShoppinglistUser
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_shoppinglist_user", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idShoppinglistUser;

    /**
     * @var integer
     *
     * @ORM\Column(name="added_by", type="integer", nullable=true)
     */
    private $addedBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var \Shoppinglist\ApiBundle\Entity\Shoppinglist
     *
     * @ORM\ManyToOne(targetEntity="Shoppinglist\ApiBundle\Entity\Shoppinglist")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fk_shoppinglist", referencedColumnName="id_shoppinglist")
     * })
     */
    private $fkShoppinglist;

    /**
     * @var \Shoppinglist\ApiBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Shoppinglist\ApiBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fk_user", referencedColumnName="id_user")
     * })
     */
    private $fkUser;



    /**
     * Get idShoppinglistUser
     *
     * @return integer 
     */
    public function getIdShoppinglistUser()
    {
        return $this->idShoppinglistUser;
    }

    /**
     * Set addedBy
     *
     * @param integer $addedBy
     * @return ShoppinglistUser
     */
    public function setAddedBy($addedBy)
    {
        $this->addedBy = $addedBy;

        return $this;
    }

    /**
     * Get addedBy
     *
     * @return integer 
     */
    public function getAddedBy()
    {
        return $this->addedBy;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return ShoppinglistUser
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

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
     * @return ShoppinglistUser
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
     * Set fkUser
     *
     * @param \Shoppinglist\ApiBundle\Entity\User $fkUser
     * @return ShoppinglistUser
     */
    public function setFkUser(\Shoppinglist\ApiBundle\Entity\User $fkUser = null)
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
}
