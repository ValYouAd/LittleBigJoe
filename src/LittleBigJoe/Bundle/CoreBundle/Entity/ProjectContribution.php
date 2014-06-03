<?php

namespace LittleBigJoe\Bundle\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProjectContribution
 *
 * @ORM\Table(name="project_contribution")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="LittleBigJoe\Bundle\CoreBundle\Repository\ProjectContributionRepository")
 */
class ProjectContribution
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
     * @var integer
     *
     * @ORM\Column(name="mangopay_contribution_id", type="integer")
     */
    protected $mangopayContributionId;

    /**
     * @var float
     *
     * @ORM\Column(name="mangopay_amount", type="decimal")
     */
    protected $mangopayAmount;

    /**
     * @var boolean
     *
     * @ORM\Column(name="mangopay_is_succeeded", type="boolean")
     */
    protected $mangopayIsSucceeded;

    /**
     * @var boolean
     *
     * @ORM\Column(name="mangopay_is_completed", type="boolean")
     */
    protected $mangopayIsCompleted;

    /**
     * @var string
     *
     * @ORM\Column(name="mangopay_error", type="string", length=255, nullable=true)
     */
    protected $mangopayError;

    /**
     * @var string
     *
     * @ORM\Column(name="mangopay_answer_code", type="string", length=255, nullable=true)
     */
    protected $mangopayAnswerCode;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="is_anonymous", type="boolean")
     */
    protected $isAnonymous;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="mangopay_refund_id", type="integer")
     */
    protected $mangopayRefundId;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="is_refunded", type="boolean")
     */
    protected $isRefunded;
    
    /**
     * @var string
     *
     * @ORM\Column(name="invoice", type="string", length=255, nullable=true)
     */
    protected $invoice;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="mangopay_created_at", type="datetime")
     */
    protected $mangopayCreatedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="mangopay_updated_at", type="datetime")
     */
    protected $mangopayUpdatedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="contributions")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     */
    protected $project;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="contributions")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="ProjectReward", inversedBy="contributions")
     * @ORM\JoinColumn(name="project_reward_id", referencedColumnName="id", nullable=true)
     */
    protected $reward;

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
    		$this->mangopayContributionId = 0;
    		$this->mangopayAmount = 0;
    		$this->mangopayIsSucceeded = false;
    		$this->mangopayIsCompleted = false;
    		$this->mangopayRefundId = 0;
    		$this->isRefunded = false;
        $this->createdAt = new \DateTime();
        $this->mangopayCreatedAt = new \DateTime();
        $this->mangopayUpdatedAt = new \DateTime();
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
     * Set mangopayContributionId
     *
     * @param integer $mangopayContributionId
     * @return ProjectContribution
     */
    public function setMangopayContributionId($mangopayContributionId)
    {
        $this->mangopayContributionId = $mangopayContributionId;

        return $this;
    }

    /**
     * Get mangopayContributionId
     *
     * @return integer
     */
    public function getMangopayContributionId()
    {
        return $this->mangopayContributionId;
    }

    /**
     * Set mangopayAmount
     *
     * @param float $mangopayAmount
     * @return ProjectContribution
     */
    public function setMangopayAmount($mangopayAmount)
    {
        $this->mangopayAmount = $mangopayAmount;

        return $this;
    }

    /**
     * Get mangopayAmount
     *
     * @return float
     */
    public function getMangopayAmount()
    {
        return $this->mangopayAmount;
    }

    /**
     * Set mangopayIsSucceeded
     *
     * @param boolean $mangopayIsSucceeded
     * @return ProjectContribution
     */
    public function setMangopayIsSucceeded($mangopayIsSucceeded)
    {
        $this->mangopayIsSucceeded = $mangopayIsSucceeded;

        return $this;
    }

    /**
     * Get mangopayIsSucceeded
     *
     * @return boolean
     */
    public function getMangopayIsSucceeded()
    {
        return $this->mangopayIsSucceeded;
    }

    /**
     * Set mangopayIsCompleted
     *
     * @param boolean $mangopayIsCompleted
     * @return ProjectContribution
     */
    public function setMangopayIsCompleted($mangopayIsCompleted)
    {
        $this->mangopayIsCompleted = $mangopayIsCompleted;

        return $this;
    }

    /**
     * Get mangopayIsCompleted
     *
     * @return boolean
     */
    public function getMangopayIsCompleted()
    {
        return $this->mangopayIsCompleted;
    }

    /**
     * Set mangopayError
     *
     * @param string $mangopayError
     * @return ProjectContribution
     */
    public function setMangopayError($mangopayError)
    {
        $this->mangopayError = $mangopayError;

        return $this;
    }

    /**
     * Get mangopayError
     *
     * @return string
     */
    public function getMangopayError()
    {
        return $this->mangopayError;
    }

    /**
     * Set mangopayAnswerCode
     *
     * @param string $mangopayAnswerCode
     * @return ProjectContribution
     */
    public function setMangopayAnswerCode($mangopayAnswerCode)
    {
        $this->mangopayAnswerCode = $mangopayAnswerCode;

        return $this;
    }

    /**
     * Get mangopayAnswerCode
     *
     * @return string
     */
    public function getMangopayAnswerCode()
    {
        return $this->mangopayAnswerCode;
    }


    /**
     * Set isAnonymous
     *
     * @param boolean $isAnonymous
     * @return ProjectContribution
     */
    public function setIsAnonymous($isAnonymous)
    {
	    	$this->isAnonymous = $isAnonymous;
	    
	    	return $this;
    }
    
    /**
     * Get isAnonymous
     *
     * @return boolean
     */
    public function getIsAnonymous()
    {
    		return $this->isAnonymous;
    }

    /**
     * Set mangopayRefundId
     *
     * @param integer $mangopayRefundId
     * @return ProjectContribution
     */
    public function setMangopayRefundId($mangopayRefundId)
    {
	    	$this->mangopayRefundId = $mangopayRefundId;
	    
	    	return $this;
    }
    
    /**
     * Get mangopayRefundId
     *
     * @return integer
     */
    public function getMangopayRefundId()
    {
    		return $this->mangopayRefundId;
    }
    
    /**
     * Set isRefunded
     *
     * @param boolean $isRefunded
     * @return ProjectContribution
     */
    public function setIsRefunded($isRefunded)
    {
	    	$this->isRefunded = $isRefunded;
	    
	    	return $this;
    }
    
    /**
     * Get isRefunded
     *
     * @return boolean
     */
    public function getIsRefunded()
    {
    		return $this->isRefunded;
    }
    
    /**
     * Set invoice
     *
     * @param string $invoice
     * @return ProjectContribution
     */
    public function setInvoice($invoice)
    {
	    	$this->invoice = $invoice;
	    
	    	return $this;
    }
    
    /**
     * Get invoice
     *
     * @return string
     */
    public function getInvoice()
    {
    		return $this->invoice;
    }
    
    /**
     * Set mangopayCreatedAt
     *
     * @param \DateTime $mangopayCreatedAt
     * @return ProjectContribution
     */
    public function setMangopayCreatedAt($mangopayCreatedAt)
    {
        $this->mangopayCreatedAt = $mangopayCreatedAt;

        return $this;
    }

    /**
     * Get mangopayCreatedAt
     *
     * @return \DateTime
     */
    public function getMangopayCreatedAt()
    {
        return $this->mangopayCreatedAt;
    }

    /**
     * Set mangopayUpdatedAt
     *
     * @param \DateTime $mangopayUpdatedAt
     * @return ProjectContribution
     */
    public function setMangopayUpdatedAt($mangopayUpdatedAt)
    {
        $this->mangopayUpdatedAt = $mangopayUpdatedAt;

        return $this;
    }

    /**
     * Get mangopayUpdatedAt
     *
     * @return \DateTime
     */
    public function getMangopayUpdatedAt()
    {
        return $this->mangopayUpdatedAt;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return ProjectContribution
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
     * Set project
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\Project $project
     * @return ProjectContribution
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
     * Set user
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\User $user
     * @return ProjectContribution
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
     * Set reward
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\ProjectReward $reward
     * @return ProjectContribution
     */
    public function setReward(\LittleBigJoe\Bundle\CoreBundle\Entity\ProjectReward $reward = null)
    {
        $this->reward = $reward;

        return $this;
    }

    /**
     * Get reward
     *
     * @return \LittleBigJoe\Bundle\CoreBundle\Entity\ProjectReward
     */
    public function getReward()
    {
        return $this->reward;
    }
}