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
 * ProjectProduct
 *
 * @ORM\Table(name="project_product")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity("name")
 * @ORM\Entity(repositoryClass="LittleBigJoe\Bundle\CoreBundle\Repository\ProjectProductRepository")
 */
class ProjectProduct
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     *
     * @Assert\NotBlank(message = "You must enter your product name", groups = {"Default", "flow_createProduct_step1"})
     * @Assert\Length(
     *    min = "2",
     *    max = "250",
     *    minMessage = "Your product name must contains at least {{ limit }} characters",
     *    maxMessage = "Your product name can't exceed {{ limit }} characters",
     *    groups = {"Default", "flow_createProduct_step1"}
     * )
     * @Assert\Regex(
     *    pattern = "/^[ÀÁÅÃÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿ0-9a-zA-Z \'-]*$/i",
     *    message = "Your product name must only contains letters, spaces, or dashes",
     *    groups = {"Default", "flow_createProduct_step1"}
     * )
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="pitch", type="text", nullable=true)
     *
     * @Assert\NotBlank(message = "You must enter the product pitch", groups = {"Default", "flow_createProduct_step1"})
     * @Assert\Length(
     *    min = "1",
     *    max = "300",
     *    minMessage = "Your product pitch must contains at least {{ limit }} characters",
     *    maxMessage = "Your product pitch can't exceed {{ limit }} characters",
     *    groups = {"Default", "flow_createProduct_step1"}
     * )
     * @Assert\Regex(
     *    pattern = "/^[ÀÁÅÃÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿa-zA-Z0-9 \-\(\)\[\]\.\,\:\;\!\']*$/i",
     *    message = "Your product pitch must only contains numbers, letters, spaces, dots, commas, exclamation marks or dashes",
     *    groups = {"Default", "flow_createProduct_step1"}
     * )
     */
    protected $pitch;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     *
     * @Assert\NotBlank(message = "You must enter the product description", groups = {"Default", "flow_createProduct_step2"})
     */
    protected $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ending_at", type="datetime", nullable=true)
     *
     * @Assert\NotBlank(message = "You must enter the product ending date", groups = {"Default", "flow_createProduct_step3"})
     * @Assert\Date(message = "Your product ending date format is incorrect", groups = {"Default", "flow_createProduct_step3"})
     */
    protected $endingAt;

    /**
     * @ORM\OneToMany(targetEntity="ProjectVideo", mappedBy="product", cascade={"persist", "remove"})
     */
    protected $videos;

    /**
     * @ORM\OneToMany(targetEntity="ProjectImage", mappedBy="product", cascade={"persist", "remove"})
     */
    protected $images;

    /**
     * Used for easier access to medias
     */
    protected $medias;

    /**
     * @var boolean
     *
     * @ORM\Column(name="gift_product", type="boolean", nullable=true)
     */
    protected $giftProduct;

    /**
     * @var integer
     *
     * @ORM\Column(name="gift_percentage_funds_raised", type="integer", nullable=true)
     */
    protected $giftPercentageFundsRaised;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="submitted_at", type="datetime", nullable=true)
     */
    protected $submittedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="validated_at", type="datetime", nullable=true)
     */
    protected $validatedAt;

    /**
     * @ORM\OneToOne(targetEntity="Project", mappedBy="product")
     */
    protected $project;

    /**
     * @ORM\OneToMany(targetEntity="ProjectProductComment", mappedBy="product", cascade={"persist", "remove"})
     */
    protected $comments;

    public function __construct()
    {
        $this->videos = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->medias = new ArrayCollection();
        $this->giftProduct = true;
        $this->giftPercentageFundsRaised = 0;
        $this->comments = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
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
     * @return ProjectProduct
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
     * Set pitch
     *
     * @param string $pitch
     * @return ProjectProduct
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
     * Set description
     *
     * @param string $description
     * @return ProjectProduct
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
        return $this->description;
    }

    /**
     * Set endingAt
     *
     * @param \DateTime $endingAt
     * @return ProjectProduct
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
     * Set giftProduct
     *
     * @param boolean $giftProduct
     * @return ProjectProduct
     */
    public function setGiftProduct($giftProduct)
    {
        $this->giftProduct = $giftProduct;
    
        return $this;
    }

    /**
     * Get giftProduct
     *
     * @return boolean 
     */
    public function getGiftProduct()
    {
        return $this->giftProduct;
    }

    /**
     * Set giftPercentageFundsRaised
     *
     * @param integer $giftPercentageFundsRaised
     * @return ProjectProduct
     */
    public function setGiftPercentageFundsRaised($giftPercentageFundsRaised)
    {
        $this->giftPercentageFundsRaised = $giftPercentageFundsRaised;
    
        return $this;
    }

    /**
     * Get giftPercentageFundsRaised
     *
     * @return integer 
     */
    public function getGiftPercentageFundsRaised()
    {
        return $this->giftPercentageFundsRaised;
    }

    /**
     * Set submittedAt
     *
     * @param \DateTime $submittedAt
     * @return ProjectProduct
     */
    public function setSubmittedAt($submittedAt)
    {
        $this->submittedAt = $submittedAt;
    
        return $this;
    }

    /**
     * Get submittedAt
     *
     * @return \DateTime 
     */
    public function getSubmittedAt()
    {
        return $this->submittedAt;
    }

    /**
     * Set validatedAt
     *
     * @param \DateTime $validatedAt
     * @return ProjectProduct
     */
    public function setValidatedAt($validatedAt)
    {
        $this->validatedAt = $validatedAt;
    
        return $this;
    }

    /**
     * Get validatedAt
     *
     * @return \DateTime 
     */
    public function getValidatedAt()
    {
        return $this->validatedAt;
    }

    /**
     * Add videos
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\ProjectVideo $videos
     * @return ProjectProduct
     */
    public function addVideo(\LittleBigJoe\Bundle\CoreBundle\Entity\ProjectVideo $videos)
    {
        $this->videos[] = $videos;
    
        return $this;
    }

    /**
     * Remove videos
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\ProjectVideo $videos
     */
    public function removeVideo(\LittleBigJoe\Bundle\CoreBundle\Entity\ProjectVideo $videos)
    {
        $this->videos->removeElement($videos);
    }

    /**
     * Get videos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getVideos()
    {
        return $this->videos;
    }

    /**
     * Add images
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\ProjectImage $images
     * @return ProjectProduct
     */
    public function addImage(\LittleBigJoe\Bundle\CoreBundle\Entity\ProjectImage $images)
    {
        $this->images[] = $images;
    
        return $this;
    }

    /**
     * Remove images
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\ProjectImage $images
     */
    public function removeImage(\LittleBigJoe\Bundle\CoreBundle\Entity\ProjectImage $images)
    {
        $this->images->removeElement($images);
    }

    /**
     * Get images
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * Get medias
     *
     * @return array
     */
    public function getMedias()
    {
        $medias = array();
        foreach ($this->images as $image)
        {
            $medias['image_'.$image->getId()] = array(
                'type' => 'image',
                'id' => $image->getId(),
                'videoUrl' => null,
                'image' => '/'.$image->getPath(),
                'highlighted' => $image->getHighlighted()
            );
        }

        foreach ($this->videos as $video)
        {
            $medias['video_'.$video->getId()] = array(
                'type' => 'video',
                'id' => $video->getId(),
                'videoUrl' => '//www.youtube.com/embed/'.$video->getProviderVideoId(),
                'image' => $video->getThumbUrl(),
                'highlighted' => $video->getHighlighted()
            );
        }

        ksort($medias);

        return $medias;
    }

    /**
     * Set project
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\Project $project
     * @return ProjectProduct
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
     * Add comments
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\ProjectProductComment $comments
     * @return ProjectProduct
     */
    public function addComment(\LittleBigJoe\Bundle\CoreBundle\Entity\ProjectProductComment $comments)
    {
        $this->comments[] = $comments;
    
        return $this;
    }

    /**
     * Remove comments
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\ProjectProductComment $comments
     */
    public function removeComment(\LittleBigJoe\Bundle\CoreBundle\Entity\ProjectProductComment $comments)
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
}