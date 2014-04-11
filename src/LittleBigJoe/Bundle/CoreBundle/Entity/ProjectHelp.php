<?php

namespace LittleBigJoe\Bundle\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProjectHelp
 * 
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="project_help")
 * @ORM\Entity(repositoryClass="LittleBigJoe\Bundle\CoreBundle\Repository\ProjectHelpRepository")
 */
class ProjectHelp
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="decimal")
     */
    protected $price;

    /**
     * @var string
     *
     * @ORM\Column(name="currency", type="string", length=255)
     */
    protected $currency;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    protected $quantity;

    /**
     * @var string
     *
     * @ORM\Column(name="reason", type="text")
     */
    protected $reason;

    /**
     * @var boolean
     *
     * @ORM\Column(name="shared_facebook", type="boolean")
     */
    protected $sharedFacebook;

    /**
     * @var boolean
     *
     * @ORM\Column(name="shared_twitter", type="boolean")
     */
    protected $sharedTwitter;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;
    
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="projectHelps")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;
    
    /**
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="projectHelps")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     */
    protected $project;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->sharedFacebook = false;
        $this->sharedTwitter = false;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->createdAt = new \DateTime();
    }
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set price
     *
     * @param float $price
     * @return ProjectHelp
     */
    public function setPrice($price)
    {
        $this->price = $price;
    
        return $this;
    }

    /**
     * Get price
     *
     * @return float 
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set currency
     *
     * @param string $currency
     * @return ProjectHelp
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    
        return $this;
    }

    /**
     * Get currency
     *
     * @return string 
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     * @return ProjectHelp
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    
        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer 
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set reason
     *
     * @param string $reason
     * @return ProjectHelp
     */
    public function setReason($reason)
    {
        $this->reason = $reason;
    
        return $this;
    }

    /**
     * Get reason
     *
     * @return string 
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * Set sharedFacebook
     *
     * @param boolean $sharedFacebook
     * @return ProjectHelp
     */
    public function setSharedFacebook($sharedFacebook)
    {
        $this->sharedFacebook = $sharedFacebook;
    
        return $this;
    }

    /**
     * Get sharedFacebook
     *
     * @return boolean 
     */
    public function getSharedFacebook()
    {
        return $this->sharedFacebook;
    }

    /**
     * Set sharedTwitter
     *
     * @param boolean $sharedTwitter
     * @return ProjectHelp
     */
    public function setSharedTwitter($sharedTwitter)
    {
        $this->sharedTwitter = $sharedTwitter;
    
        return $this;
    }

    /**
     * Get sharedTwitter
     *
     * @return boolean 
     */
    public function getSharedTwitter()
    {
        return $this->sharedTwitter;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return ProjectHelp
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
     * Set user
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\User $user
     * @return ProjectHelp
     */
    public function setUser(\LittleBigJoe\Bundle\CoreBundle\Entity\User $user = null)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return \LittleBigJoe\Bundle\CoreBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set project
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\Project $project
     * @return ProjectHelp
     */
    public function setProject(\LittleBigJoe\Bundle\CoreBundle\Entity\Project $project = null)
    {
        $this->project = $project;
    
        return $this;
    }

    /**
     * Get project
     *
     * @return \LittleBigJoe\Bundle\CoreBundle\Entity\Project 
     */
    public function getProject()
    {
        return $this->project;
    }
}