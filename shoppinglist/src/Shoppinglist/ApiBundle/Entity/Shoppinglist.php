<?php

namespace Shoppinglist\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Shoppinglist
 *
 * @ORM\Table(name="shoppinglist", indexes={@ORM\Index(name="id_user", columns={"fk_user"})})
 * @ORM\Entity(repositoryClass="Shoppinglist\ApiBundle\Entity\ShoppinglistRepository")
 */
class Shoppinglist
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_shoppinglist", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $idShoppinglist;

    /**
     * @var string
     * 
     * @Assert\NotBlank(message="Shopping name should not be blank")
     * @ORM\Column(name="shoppinglist_name", type="string", length=65, nullable=false)
     */
    private $shoppinglistName;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var \Shoppinglist\ApiBundle\Entity\User
     *
     * @Assert\NotBlank(message="No user found")
     * @ORM\ManyToOne(targetEntity="Shoppinglist\ApiBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fk_user", referencedColumnName="id_user")
     * })
     */
    private $fkUser;



    /**
     * Get idShoppinglist
     *
     * @return integer 
     */
    public function getIdShoppinglist()
    {
        return $this->idShoppinglist;
    }

    /**
     * Set shoppinglistName
     *
     * @param string $shoppinglistName
     * @return Shoppinglist
     */
    public function setShoppinglistName($shoppinglistName)
    {
        $this->shoppinglistName = $shoppinglistName;

        return $this;
    }

    /**
     * Get shoppinglistName
     *
     * @return string 
     */
    public function getShoppinglistName()
    {
        return $this->shoppinglistName;
    }

    /**
     * Set status
     *
     * @param boolean $status
     * @return Shoppinglist
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set createdAt
     *
     * @return Shoppinglist
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
     * Set fkUser
     *
     * @param \Shoppinglist\ApiBundle\Entity\User $fkUser
     * @return Shoppinglist
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
