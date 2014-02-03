<?php

namespace LittleBigJoe\Bundle\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Withdrawal
 *
 * @ORM\Table(name="withdrawal")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="LittleBigJoe\Bundle\CoreBundle\Repository\WithdrawalRepository")
 */
class Withdrawal
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
     * @ORM\Column(name="mangopay_withdrawal_id", type="integer")
     */
    private $mangopayWithdrawalId;

    /**
     * @var float
     *
     * @ORM\Column(name="mangopay_amount", type="decimal")
     */
    private $mangopayAmount;
    
    /**
     * @var float
     *
     * @ORM\Column(name="mangopay_amount_without_fees", type="decimal")
     */
    private $mangopayAmountWithoutFees;
    
    /**
     * @var float
     *
     * @ORM\Column(name="mangopay_client_fee_amount", type="decimal")
     */
    private $mangopayClientFeeAmount;

    /**
     * @var boolean
     *
     * @ORM\Column(name="mangopay_is_succeeded", type="boolean")
     */
    private $mangopayIsSucceeded;

    /**
     * @var boolean
     *
     * @ORM\Column(name="mangopay_is_completed", type="boolean")
     */
    private $mangopayIsCompleted;

    /**
     * @var string
     *
     * @ORM\Column(name="mangopay_error", type="string", length=255, nullable=true)
     */
    private $mangopayError;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="mangopay_created_at", type="datetime")
     */
    private $mangopayCreatedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="mangopay_updated_at", type="datetime")
     */
    private $mangopayUpdatedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;
    
    /**
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="withdrawals")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     */
    protected $project;
    
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="withdrawals")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;
    
    /**
     * @ORM\ManyToOne(targetEntity="Beneficiary", inversedBy="withdrawals")
     * @ORM\JoinColumn(name="beneficiary_id", referencedColumnName="id")
     */
    protected $beneficiary;
    
    public function __construct()
    {
	    	$this->mangopayWithdrawalId = 0;
	    	$this->mangopayAmount = 0;
	    	$this->mangopayAmountWithoutFees = 0;
	    	$this->mangopayClientFeeAmount = 0;
	    	$this->mangopayIsSucceeded = false;
	    	$this->mangopayIsCompleted = false;
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
     * Set mangopayWithdrawalId
     *
     * @param integer $mangopayWithdrawalId
     * @return Withdrawal
     */
    public function setMangopayWithdrawalId($mangopayWithdrawalId)
    {
        $this->mangopayWithdrawalId = $mangopayWithdrawalId;
    
        return $this;
    }

    /**
     * Get mangopayWithdrawalId
     *
     * @return integer 
     */
    public function getMangopayWithdrawalId()
    {
        return $this->mangopayWithdrawalId;
    }

    /**
     * Set mangopayAmount
     *
     * @param float $mangopayAmount
     * @return Withdrawal
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
     * Set mangopayAmountWithoutFees
     *
     * @param float $mangopayAmountWithoutFees
     * @return Withdrawal
     */
    public function setMangopayAmountWithoutFees($mangopayAmountWithoutFees)
    {
	    	$this->mangopayAmountWithoutFees = $mangopayAmountWithoutFees;
	    
	    	return $this;
    }
    
    /**
     * Get mangopayAmountWithoutFees
     *
     * @return float
     */
    public function getMangopayAmountWithoutFees()
    {
    		return $this->mangopayAmountWithoutFees;
    }
    
    /**
     * Set mangopayClientFeeAmount
     *
     * @param float $mangopayClientFeeAmount
     * @return Withdrawal
     */
    public function setMangopayClientFeeAmount($mangopayClientFeeAmount)
    {
	    	$this->mangopayClientFeeAmount = $mangopayClientFeeAmount;
	    
	    	return $this;
    }
    
    /**
     * Get mangopayClientFeeAmount
     *
     * @return float
     */
    public function getMangopayClientFeeAmount()
    {
    		return $this->mangopayClientFeeAmount;
    }
    
    /**
     * Set mangopayIsSucceeded
     *
     * @param boolean $mangopayIsSucceeded
     * @return Withdrawal
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
     * @return Withdrawal
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
     * @return Withdrawal
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
     * Set mangopayCreatedAt
     *
     * @param \DateTime $mangopayCreatedAt
     * @return Withdrawal
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
     * @return Withdrawal
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
     * @return Withdrawal
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
     * @return Withdrawal
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
     * @return Withdrawal
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
     * Set beneficiary
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\Beneficiary $beneficiary
     * @return Withdrawal
     */
    public function setBeneficiary(\LittleBigJoe\Bundle\CoreBundle\Entity\Beneficiary $beneficiary = null)
    {
        $this->beneficiary = $beneficiary;
    
        return $this;
    }

    /**
     * Get beneficiary
     *
     * @return \LittleBigJoe\Bundle\CoreBundle\Entity\Beneficiary 
     */
    public function getBeneficiary()
    {
        return $this->beneficiary;
    }
}