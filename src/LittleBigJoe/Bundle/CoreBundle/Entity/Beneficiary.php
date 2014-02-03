<?php

namespace LittleBigJoe\Bundle\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Beneficiary
 *
 * @ORM\Table(name="beneficiary")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="LittleBigJoe\Bundle\CoreBundle\Repository\BeneficiaryRepository")
 */
class Beneficiary
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="bank_account_owner_name", type="string", length=255)
     */
    private $bankAccountOwnerName;

    /**
     * @var string
     *
     * @ORM\Column(name="bank_account_owner_address", type="string", length=255)
     */
    private $bankAccountOwnerAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="bank_account_iban", type="string", length=255)
     */
    private $bankAccountIban;

    /**
     * @var string
     *
     * @ORM\Column(name="bank_account_bic", type="string", length=255)
     */
    private $bankAccountBic;

    /**
     * @var integer
     *
     * @ORM\Column(name="mangopay_beneficiary_id", type="integer")
     */
    private $mangopayBeneficiaryId;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="beneficiaries")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;
    
    /**
     * @ORM\OneToMany(targetEntity="Withdrawal", mappedBy="beneficiary", cascade={"persist", "remove"})
     */
    protected $withdrawals;

    public function __toString()
    {
    		return $this->name;
    }
    
    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
	    	$this->mangopayBeneficiaryId = 0;
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
     * Set name
     *
     * @param string $name
     * @return Beneficiary
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set bankAccountOwnerName
     *
     * @param string $bankAccountOwnerName
     * @return Beneficiary
     */
    public function setBankAccountOwnerName($bankAccountOwnerName)
    {
        $this->bankAccountOwnerName = $bankAccountOwnerName;
    
        return $this;
    }

    /**
     * Get bankAccountOwnerName
     *
     * @return string 
     */
    public function getBankAccountOwnerName()
    {
        return $this->bankAccountOwnerName;
    }

    /**
     * Set bankAccountOwnerAddress
     *
     * @param string $bankAccountOwnerAddress
     * @return Beneficiary
     */
    public function setBankAccountOwnerAddress($bankAccountOwnerAddress)
    {
        $this->bankAccountOwnerAddress = $bankAccountOwnerAddress;
    
        return $this;
    }

    /**
     * Get bankAccountOwnerAddress
     *
     * @return string 
     */
    public function getBankAccountOwnerAddress()
    {
        return $this->bankAccountOwnerAddress;
    }

    /**
     * Set bankAccountIban
     *
     * @param string $bankAccountIban
     * @return Beneficiary
     */
    public function setBankAccountIban($bankAccountIban)
    {
        $this->bankAccountIban = $bankAccountIban;
    
        return $this;
    }

    /**
     * Get bankAccountIban
     *
     * @return string 
     */
    public function getBankAccountIban()
    {
        return $this->bankAccountIban;
    }

    /**
     * Set bankAccountBic
     *
     * @param string $bankAccountBic
     * @return Beneficiary
     */
    public function setBankAccountBic($bankAccountBic)
    {
        $this->bankAccountBic = $bankAccountBic;
    
        return $this;
    }

    /**
     * Get bankAccountBic
     *
     * @return string 
     */
    public function getBankAccountBic()
    {
        return $this->bankAccountBic;
    }

    /**
     * Set mangopayBeneficiaryId
     *
     * @param integer $mangopayBeneficiaryId
     * @return Beneficiary
     */
    public function setMangopayBeneficiaryId($mangopayBeneficiaryId)
    {
        $this->mangopayBeneficiaryId = $mangopayBeneficiaryId;
    
        return $this;
    }

    /**
     * Get mangopayBeneficiaryId
     *
     * @return integer 
     */
    public function getMangopayBeneficiaryId()
    {
        return $this->mangopayBeneficiaryId;
    }

    /**
     * Set user
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\User $user
     * @return Beneficiary
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
}