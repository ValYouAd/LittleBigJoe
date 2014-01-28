<?php

namespace LittleBigJoe\Bundle\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Notification
 * 
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="notification")
 * @ORM\Entity(repositoryClass="LittleBigJoe\Bundle\CoreBundle\Repository\NotificationRepository")
 */
class Notification
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="integer")
     * 0 => 'project likes step'
     * 1 => 'project funders step'
     * 2 => 'new project entry'
     */
    private $type;

    /**
     * @var array
     *
     * @ORM\Column(name="data", type="array", nullable=true)
     */
    private $data;    

    /**
     * @var boolean
     *
     * @ORM\Column(name="viewed", type="boolean")
     */
    private $viewed;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;
    
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="notifications")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;
    
    /**
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="notifications")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     */
    protected $project;
    
    /**
     * @ORM\ManyToOne(targetEntity="Entry", inversedBy="notifications")
     * @ORM\JoinColumn(name="entry_id", referencedColumnName="id")
     */
    protected $entry;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->data = array();
        $this->viewed = false;
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
     * Set type
     *
     * @param integer $type
     * @return Notification
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set data
     *
     * @param array $data
     * @return Notification
     */
    public function setData($data)
    {
        $this->data = $data;
    
        return $this;
    }

    /**
     * Get data
     *
     * @return array 
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set viewed
     *
     * @param boolean $viewed
     * @return Notification
     */
    public function setViewed($viewed)
    {
        $this->viewed = $viewed;
    
        return $this;
    }
    
    /**
     * Get viewed
     *
     * @return boolean
     */
    public function getViewed()
    {
        return $this->viewed;
    }
    
    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Notification
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
     * @return Notification
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
     * @return Notification
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

    /**
     * Set entry
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\Entry $entry
     * @return Notification
     */
    public function setEntry(\LittleBigJoe\Bundle\CoreBundle\Entity\Entry $entry = null)
    {
        $this->entry = $entry;
    
        return $this;
    }

    /**
     * Get entry
     *
     * @return \LittleBigJoe\Bundle\CoreBundle\Entity\Entry 
     */
    public function getEntry()
    {
        return $this->entry;
    }
}