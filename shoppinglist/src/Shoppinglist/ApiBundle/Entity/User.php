<?php

namespace Shoppinglist\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * User
 *
 * @ORM\Table(name="user")
 * @UniqueEntity("email", message="Provided email already exists.")
 * @ORM\Entity(repositoryClass="Shoppinglist\ApiBundle\Entity\UserRepository")
 */
class User
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_user", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $idUser;

    /**
     * @var string
     * 
     * @Assert\NotBlank(message="Email should not be blank")
     * @Assert\Email(message="Provided email is not valid.")
     * @ORM\Column(name="email", type="string", length=100)
     */
    private $email;

    /**
     * @var string
     * 
     * @Assert\NotBlank(message="First name should not be blank.")
     * @ORM\Column(name="first_name", type="string", length=45)
     */
    private $firstName;

    /**
     * @var string
     * 
     * @Assert\NotBlank(message="Last name should not be blank.")
     * @ORM\Column(name="last_name", type="string", length=45)
     */
    private $lastName;

    /**
     * @var integer
     * 
     * @Assert\NotBlank(message="Gender should not be blank.")
     * @ORM\Column(name="gender", type="smallint")
     */
    private $gender;

    /**
     * @var string
     * 
     * @Assert\NotBlank(message="Password should not be blank.")
     * @ORM\Column(name="pass", type="string", length=255)
     */
    private $pass;

    /**
     * @var string
     * 
     * @ORM\Column(name="mobile_no", type="string", length=15)
     */
    private $mobileNo;

    /**
     * @var string
     *
     * @ORM\Column(name="api_key", type="string", length=64)
     */
    private $apiKey;
    
    /**
     * @var string
     *
     * @ORM\Column(name="email_verification_code", type="string", length=6)
     */
    private $emailVerificationCode;
    
    /**
     * @var string
     *
     * @ORM\Column(name="password_verification_code", type="string", length=6)
     */
    private $passwordVerificationCode;

    /**
     * @var integer
     *
     * @ORM\Column(name="email_verified", type="smallint")
     */
    private $emailVerified;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="smallint")
     */
    private $status;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_admin", type="smallint", nullable=false)
     */
    private $isAdmin;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;



    /**
     * Get idUser
     *
     * @return integer 
     */
    public function getIdUser()
    {
        return $this->idUser;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string 
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string 
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set gender
     *
     * @param boolean $gender
     * @return User
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return boolean 
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set pass
     *
     * @param string $pass
     * @return User
     */
    public function setPass($pass)
    {
        $this->pass = $pass;

        return $this;
    }

    /**
     * Get pass
     *
     * @return string 
     */
    public function getPass()
    {
        return $this->pass;
    }

    /**
     * Set mobileNo
     *
     * @param string $mobileNo
     * @return User
     */
    public function setMobileNo($mobileNo)
    {
        $this->mobileNo = $mobileNo;

        return $this;
    }

    /**
     * Get mobileNo
     *
     * @return string 
     */
    public function getMobileNo()
    {
        return $this->mobileNo;
    }

    /**
     * Set apiKey
     *
     * @param string $apiKey
     * @return User
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * Get apiKey
     *
     * @return string 
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }
    
    /**
     * Set emailVerificationCode
     *
     * @param string $emailVerificationCode
     * @return User
     */
    public function setEmailVerificationCode($emailVerificationCode)
    {
        $this->emailVerificationCode = $emailVerificationCode;

        return $this;
    }

    /**
     * Get emailVerificationCode
     *
     * @return string 
     */
    public function getEmailVerificationCode()
    {
        return $this->emailVerificationCode;
    }
    
    /**
     * Set passwordVerificationCode
     *
     * @param string $passwordVerificationCode
     * @return User
     */
    public function setPasswordVerificationCode($passwordVerificationCode)
    {
        $this->passwordVerificationCode = $passwordVerificationCode;

        return $this;
    }

    /**
     * Get passwordVerificationCode
     *
     * @return string 
     */
    public function getPasswordVerificationCode()
    {
        return $this->passwordVerificationCode;
    }
    
    /**
     * Set emailVerified
     *
     * @param int $emailVerified
     * @return User
     */
    public function setEmailVerified($emailVerified)
    {
        $this->emailVerified = $emailVerified;

        return $this;
    }

    /**
     * Get emailVerified
     *
     * @return int 
     */
    public function getEmailVerified()
    {
        return $this->emailVerified;
    }

    /**
     * Set status
     *
     * @param int $status
     * @return User
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return int 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set isAdmin
     *
     * @param int $isAdmin
     * @return User
     */
    public function setIsAdmin($isAdmin)
    {
        $this->isAdmin = $isAdmin;

        return $this;
    }

    /**
     * Get isAdmin
     *
     * @return int 
     */
    public function getIsAdmin()
    {
        return $this->isAdmin;
    }

    /**
     * Set createdAt
     *
     * @return User
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
     * Set updatedAt
     *
     * @return User
     */
    public function setUpdatedAt()
    {
        $this->updatedAt = new \DateTime();

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}
