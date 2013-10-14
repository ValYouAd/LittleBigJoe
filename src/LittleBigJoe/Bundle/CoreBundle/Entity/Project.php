<?php

namespace LittleBigJoe\Bundle\CoreBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\ExecutionContext;

/**
 * Project
 *
 * @ORM\Table(name="project")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity("name")
 * @UniqueEntity("slug")
 * @Assert\Callback(methods = {"isDateInFuture"}, groups = {"Default", "flow_createProject_step2"})
 * @ORM\Entity(repositoryClass="LittleBigJoe\Bundle\CoreBundle\Repository\ProjectRepository")
 * @Gedmo\Uploadable(filenameGenerator="SHA1", allowOverwrite=true, appendNumber=true, allowedTypes="image/png,image/jpg,image/jpeg,image/gif")
 */
class Project
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
     *
     * @Assert\NotBlank(message = "You must enter your project name", groups = {"Default", "flow_createProject_step1"})
     * @Assert\Length(
     *    min = "2",
     *    max = "250",
     *    minMessage = "Your project name must contains at least {{ limit }} characters",
     *    maxMessage = "Your project name can't exceed {{ limit }} characters", 
     *    groups = {"Default", "flow_createProject_step1"}
     * )
     * @Assert\Regex(
     *    pattern = "/^[ÀÁÅÃÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿ0-9a-zA-Z \'-]*$/i",
     *    message = "Your project name must only contains letters, spaces, or dashes",
     *    groups = {"Default", "flow_createProject_step1"}
     * )
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255)
     *
     * @Assert\NotBlank(message = "You must enter your project slug", groups = {"Default", "flow_createProject_step1"})
     * @Assert\Regex(
     *    pattern = "/^[ÀÁÅÃÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿ0-9a-zA-Z\-]*$/i",
     *    message = "Your project slug must only contains letters, or dashes",
     *    groups = {"Default", "flow_createProject_step1"}
     * )
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="photo", type="string", length=255, nullable=true)
     * @Gedmo\UploadableFilePath
     */
    private $photo;

    /**
     * @var string
     *
     * @ORM\Column(name="location", type="string", length=255)
     *
     * @Assert\NotBlank(message = "You must enter your location", groups = {"Default", "flow_createProject_step1"})
     * @Assert\Length(
     *    min = "2",
     *    max = "250",
     *    minMessage = "Your location must contains at least {{ limit }} characters",
     *    maxMessage = "Your location can't exceed {{ limit }} characters",
     *    groups = {"Default", "flow_createProject_step1"}
     * )
     * @Assert\Regex(
     *    pattern = "/^[ÀÁÅÃÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿa-zA-Z0-9 \'\-\,]*$/i",
     *    message = "Your bio must only contains numbers, letters, spaces, commas or dashes",
     *    groups = {"Default", "flow_createProject_step1"}
     * )
     */
    private $location;

    /**
     * @var string
     *
     * @ORM\Column(name="pitch", type="string", length=255)
     *
     * @Assert\NotBlank(message = "You must enter the pitch", groups = {"Default", "flow_createProject_step1"})
     * @Assert\Regex(
     *    pattern = "/^[ÀÁÅÃÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿa-zA-Z0-9 \-\(\)\[\]\.\,\:\;\!\']*$/i",
     *    message = "Your pitch must only contains numbers, letters, spaces, dots, commas, exclamation marks or dashes",
     *    groups = {"Default", "flow_createProject_step1"}
     * )
     */
    private $pitch;

    /**
     * @var string
     *
     * @ORM\Column(name="language", type="string", length=45)
     */
    private $language;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     *
     * @Assert\NotBlank(message = "You must enter the description", groups = {"Default", "flow_createProject_step3"})
     */
    private $description;

    /**
     * @var float
     *
     * @ORM\Column(name="amount_required", type="decimal")
     *
     * @Assert\NotBlank(message = "You must enter the required amount", groups = {"Default", "flow_createProject_step2"})
     * @Assert\Regex(
     *    pattern = "/^[0-9\.\,]*$/",
     *    message = "Your required amount must only contains numbers, dots, or commas",
     *    groups = {"Default", "flow_createProject_step2"}
     * )
     */
    private $amountRequired;

    /**
     * @var float
     *
     * @ORM\Column(name="amount_count", type="decimal", nullable=true)
     */
    private $amountCount;

    /**
     * @var integer
     *
     * @ORM\Column(name="likes_required", type="integer")
     *
     * @Assert\NotBlank(message = "You must enter the required likes count", groups = {"Default", "flow_createProject_step2"})
     * @Assert\Regex(
     *    pattern = "/^[0-9]*$/",
     *    message = "Your required likes count must only contains numbers",
     *    groups = {"Default", "flow_createProject_step2"}
     * )
     */
    private $likesRequired;

    /**
     * @var integer
     *
     * @ORM\Column(name="likes_count", type="integer", nullable=true)
     */
    private $likesCount;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var integer
     *
     * @ORM\Column(name="mangopay_wallet_id", type="integer", nullable=true)
     */
    private $mangopayWalletId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="mangopay_created_at", type="datetime", nullable=true)
     */
    private $mangopayCreatedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="mangopay_updated_at", type="datetime", nullable=true)
     */
    private $mangopayUpdatedAt;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="status_updated_at", type="datetime", nullable=true)
     */
    private $statusUpdatedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ended_at", type="datetime", nullable=true)
     */
    private $endedAt;    
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ending_at", type="datetime")
     *
     * @Assert\NotBlank(message = "You must enter the project ending date", groups = {"Default", "flow_createProject_step2"})
     * @Assert\DateTime(message = "Your project ending date format is incorrect", groups = {"Default", "flow_createProject_step2"})
     */
    private $endingAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true)
     */
    private $deletedAt;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_favorite", type="boolean")
     */
    private $isFavorite;

    /**
     * @ORM\ManyToOne(targetEntity="Brand", inversedBy="projects")
     * @ORM\JoinColumn(name="brand_id", referencedColumnName="id")
     */
    protected $brand;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="projects")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    protected $category;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="projects")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\OneToMany(targetEntity="Entry", mappedBy="project", cascade={"persist", "remove"})
     */
    protected $entries;

    /**
     * @ORM\OneToMany(targetEntity="ProjectReward", mappedBy="project", cascade={"persist", "remove"})
     * @ORM\OrderBy({"amount" = "ASC"})
     */
    protected $rewards;

    /**
     * @ORM\OneToMany(targetEntity="ProjectContribution", mappedBy="project", cascade={"persist", "remove"})
     */
    protected $contributions;

    /**
     * @ORM\OneToMany(targetEntity="ProjectLike", mappedBy="project", cascade={"persist", "remove"})
     */
    protected $likes;
    
    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="project", cascade={"persist", "remove"})
     */
    protected $comments;
    
    /**
     * @ORM\OneToMany(targetEntity="Withdrawal", mappedBy="project", cascade={"persist", "remove"})
     */
    protected $withdrawals;
    
    public function __construct()
    {
        $this->entries = new ArrayCollection();
        $this->rewards = new ArrayCollection();
        $this->contributions = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->withdrawals = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->amountCount = 0;
        $this->likesCount = 0;
        $this->status = '1';
        $this->isFavorite = false;
        $this->createdAt = new \DateTime();
        $this->mangopayWalletId = 0;
        $this->mangopayCreatedAt = new \DateTime();
        $this->mangopayUpdatedAt = new \DateTime();
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->updatedAt = new \DateTime();
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
     * @return Project
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
     * Set slug
     *
     * @param string $slug
     * @return Project
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set photo
     *
     * @param string $photo
     * @return Project
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * Get photo
     *
     * @return string
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * Set location
     *
     * @param string $location
     * @return Project
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set pitch
     *
     * @param string $pitch
     * @return Project
     */
    public function setPitch($pitch)
    {
        $this->pitch = $pitch;

        return $this;
    }

    /**
     * Get pitch
     *
     * @return string
     */
    public function getPitch()
    {
        return $this->pitch;
    }

    /**
     * Set language
     *
     * @param string $language
     * @return Project
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get language
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Project
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return html_entity_decode($this->description);
    }

    /**
     * Set amountRequired
     *
     * @param float $amountRequired
     * @return Project
     */
    public function setAmountRequired($amountRequired)
    {
        $this->amountRequired = $amountRequired;

        return $this;
    }

    /**
     * Get amountRequired
     *
     * @return float
     */
    public function getAmountRequired()
    {
        return $this->amountRequired;
    }

    /**
     * Set amountCount
     *
     * @param float $amountCount
     * @return Project
     */
    public function setAmountCount($amountCount)
    {
        $this->amountCount = $amountCount;

        return $this;
    }

    /**
     * Get amountCount
     *
     * @return float
     */
    public function getAmountCount()
    {
        return $this->amountCount;
    }

    /**
     * Set likesRequired
     *
     * @param integer $likesRequired
     * @return Project
     */
    public function setLikesRequired($likesRequired)
    {
        $this->likesRequired = $likesRequired;

        return $this;
    }

    /**
     * Get likesRequired
     *
     * @return integer
     */
    public function getLikesRequired()
    {
        return $this->likesRequired;
    }

    /**
     * Set likesCount
     *
     * @param integer $likesCount
     * @return Project
     */
    public function setLikesCount($likesCount)
    {
        $this->likesCount = $likesCount;

        return $this;
    }

    /**
     * Get likesCount
     *
     * @return integer
     */
    public function getLikesCount()
    {
        return $this->likesCount;
    }

    /**
     * Set mangopayWalletId
     *
     * @param integer $mangopayWalletId
     * @return Project
     */
    public function setMangopayWalletId($mangopayWalletId)
		{
        $this->mangopayWalletId = $mangopayWalletId;

        return $this;
    }

    /**
     * Get mangopayWalletId
     *
     * @return integer
     */
    public function getMangopayWalletId()
    {
        return $this->mangopayWalletId;
    }

    /**
     * Set mangopayCreatedAt
     *
     * @param \DateTime $mangopayCreatedAt
     * @return Project
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
     * @return Project
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
     * Set status
     *
     * @param integer $status
     * @return Project
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set statusUpdatedAt
     *
     * @param \DateTime $statusUpdatedAt
     * @return Project
     */
    public function setStatusUpdatedAt($statusUpdatedAt)
    {
        $this->statusUpdatedAt = $statusUpdatedAt;

        return $this;
    }

    /**
     * Get statusUpdatedAt
     *
     * @return \DateTime
     */
    public function getStatusUpdatedAt()
    {
        return $this->statusUpdatedAt;
    }

    /**
     * Set endedAt
     *
     * @param \DateTime $endedAt
     * @return Project
     */
    public function setEndedAt($endedAt)
    {
	    	$this->endedAt = $endedAt;
	    
	    	return $this;
    }
    
    /**
     * Get endedAt
     *
     * @return \DateTime
     */
    public function getEndedAt()
    {
    		return $this->endedAt;
    }
    
    /**
     * Set endingAt
     *
     * @param \DateTime $endingAt
     * @return Project
     */
    public function setEndingAt($endingAt)
    {
        $this->endingAt = $endingAt;

        return $this;
    }

    /**
     * Get endingAt
     *
     * @return \DateTime
     */
    public function getEndingAt()
    {
        return $this->endingAt;
    }

    /**
     * Set deletedAt
     *
     * @param \DateTime $deletedAt
     * @return Project
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * Get deletedAt
     *
     * @return \DateTime
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * Set isFavorite
     *
     * @param boolean $isFavorite
     * @return Project
     */
    public function setIsFavorite($isFavorite)
    {
        $this->isFavorite = $isFavorite;

        return $this;
    }

    /**
     * Get isFavorite
     *
     * @return boolean
     */
    public function getIsFavorite()
    {
        return $this->isFavorite;
    }

    /**
     * Set brand
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\Brand $brand
     * @return Project
     */
    public function setBrand(\LittleBigJoe\Bundle\CoreBundle\Entity\Brand $brand = null)
    {
        $this->brand = $brand;

        return $this;
    }

    /**
     * Get brand
     *
     * @return \LittleBigJoe\Bundle\CoreBundle\Entity\Brand
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * Set category
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\Category $category
     * @return Project
     */
    public function setCategory(\LittleBigJoe\Bundle\CoreBundle\Entity\Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \LittleBigJoe\Bundle\CoreBundle\Entity\Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Add entries
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\Entry $entries
     * @return Project
     */
    public function addEntrie(\LittleBigJoe\Bundle\CoreBundle\Entity\Entry $entries)
    {
        $this->entries[] = $entries;

        return $this;
    }

    /**
     * Remove entries
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\Entry $entries
     */
    public function removeEntrie(\LittleBigJoe\Bundle\CoreBundle\Entity\Entry $entries)
    {
        $this->entries->removeElement($entries);
    }

    /**
     * Get entries
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEntries()
    {
        return $this->entries;
    }

    /**
     * Add rewards
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\ProjectReward $rewards
     * @return Project
     */
    public function addReward(\LittleBigJoe\Bundle\CoreBundle\Entity\ProjectReward $rewards)
    {
        $this->rewards[] = $rewards;

        return $this;
    }

    /**
     * Remove rewards
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\ProjectReward $rewards
     */
    public function removeReward(\LittleBigJoe\Bundle\CoreBundle\Entity\ProjectReward $rewards)
    {
        $this->rewards->removeElement($rewards);
    }

    /**
     * Get rewards
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRewards()
    {
        return $this->rewards;
    }

    /**
     * Add contributions
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\ProjectContribution $contributions
     * @return Project
     */
    public function addContribution(\LittleBigJoe\Bundle\CoreBundle\Entity\ProjectContribution $contributions)
    {
        $this->contributions[] = $contributions;

        return $this;
    }

    /**
     * Remove contributions
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\ProjectContribution $contributions
     */
    public function removeContribution(\LittleBigJoe\Bundle\CoreBundle\Entity\ProjectContribution $contributions)
    {
        $this->contributions->removeElement($contributions);
    }

    /**
     * Get contributions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getContributions()
    {
        return $this->contributions;
    }

    /**
     * Add likes
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\ProjectLike $likes
     * @return Project
     */
    public function addLike(\LittleBigJoe\Bundle\CoreBundle\Entity\ProjectLike $likes)
    {
        $this->likes[] = $likes;

        return $this;
    }

    /**
     * Remove likes
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\ProjectLike $likes
     */
    public function removeLike(\LittleBigJoe\Bundle\CoreBundle\Entity\ProjectLike $likes)
    {
        $this->likes->removeElement($likes);
    }

    /**
     * Get likes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLikes()
    {
        return $this->likes;
    }

    /**
     * Set user
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\User $user
     * @return Project
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Project
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
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Project
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

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
    
    /**
     * Used to check if endingAt is in the future (during project creation)
     *
     * @return boolean
     */
    public function isDateInFuture(ExecutionContext $context)
		{
		    $propertyPath = $context->getPropertyPath();
		
		    if ($this->endingAt < new \DateTime()) 
		    {
		        $context->addViolationAt('endingAt', 'The ending date cannot be in the past', array(), null);
		    }
		}

    /**
     * Add comments
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\Comment $comments
     * @return Project
     */
    public function addComment(\LittleBigJoe\Bundle\CoreBundle\Entity\Comment $comments)
    {
        $this->comments[] = $comments;
    
        return $this;
    }

    /**
     * Remove comments
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\Comment $comments
     */
    public function removeComment(\LittleBigJoe\Bundle\CoreBundle\Entity\Comment $comments)
    {
        $this->comments->removeElement($comments);
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Add withdrawals
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\Withdrawal $withdrawals
     * @return Project
     */
    public function addWithdrawal(\LittleBigJoe\Bundle\CoreBundle\Entity\Withdrawal $withdrawals)
    {
        $this->withdrawals[] = $withdrawals;
    
        return $this;
    }

    /**
     * Remove withdrawals
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\Withdrawal $withdrawals
     */
    public function removeWithdrawal(\LittleBigJoe\Bundle\CoreBundle\Entity\Withdrawal $withdrawals)
    {
        $this->withdrawals->removeElement($withdrawals);
    }

    /**
     * Get withdrawals
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getWithdrawals()
    {
        return $this->withdrawals;
    }
}