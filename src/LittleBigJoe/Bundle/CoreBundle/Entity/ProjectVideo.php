<?php

namespace LittleBigJoe\Bundle\CoreBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * Video
 *
 * @ORM\Table(name="project_video")
 * @ORM\Entity(repositoryClass="LittleBigJoe\Bundle\CoreBundle\Repository\ProjectVideoRepository")
 */
class ProjectVideo
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * @var string
     *
     * @ORM\Column(name="provider_name", type="string", length=255)
     */
    protected $providerName;

    /**
     * @var array
     *
     * @ORM\Column(name="provider_metadata", type="array")
     */
    protected $providerMetadata;

    /**
     * @var integer
     *
     * @ORM\Column(name="provider_video_id", type="string", length=255)
     */
    protected $providerVideoId;

    /**
     * @var integer
     *
     * @ORM\Column(name="width", type="integer", nullable=true)
     */
    protected $width;

    /**
     * @var integer
     *
     * @ORM\Column(name="height", type="integer", nullable=true)
     */
    protected $height;

    /**
     * @var float
     *
     * @ORM\Column(name="length", type="decimal", nullable=true)
     */
    protected $length;

    /**
     * @var string $embed_player_code
     *
     * @ORM\Column(name="embed_player_code", type="text", nullable=true)
     */
    protected $embedPlayerCode;

    /**
     * @var bigint $views
     *
     * @ORM\Column(name="views", type="bigint")
     */
    protected $views = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="thumb_width", type="integer")
     */
    protected $thumbWidth;

    /**
     * @var integer
     *
     * @ORM\Column(name="thumb_height", type="integer")
     */
    protected $thumbHeight;

    /**
     * @ORM\Column(name="thumb_url", type="string", length=255)
     */
    protected $thumbUrl;

    /**
     * @var boolean
     *
     * @ORM\Column(name="visibible", type="boolean")
     */
    protected $visible = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="hide", type="boolean")
     */
    protected $hide = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="highlighted", type="boolean")
     */
    protected $highlighted = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="waiting_validation", type="boolean")
     */
    protected $waitingValidation = false;

    /**
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="videos")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id", nullable=true)
     */
    protected $project;

    /**
     * @ORM\ManyToOne(targetEntity="ProjectProduct", inversedBy="videos")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id", nullable=true)
     */
    protected $product;

    public function __toString()
    {
        return $this->name;
    }

    public function __construct()
    {

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
     * @return Video
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
     * Set description
     *
     * @param string $description
     * @return Video
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
     * Set providerName
     *
     * @param string $providerName
     * @return Video
     */
    public function setProviderName($providerName)
    {
        $this->providerName = $providerName;
    
        return $this;
    }

    /**
     * Get providerName
     *
     * @return string 
     */
    public function getProviderName()
    {
        return $this->providerName;
    }

    /**
     * Set providerMetadata
     *
     * @param array $providerMetadata
     * @return Video
     */
    public function setProviderMetadata($providerMetadata)
    {
        $this->providerMetadata = $providerMetadata;

        if (array_key_exists('width', $providerMetadata))
            $this->setWidth($providerMetadata['width']);
        if (array_key_exists('height', $providerMetadata))
            $this->setHeight($providerMetadata['height']);
        if (array_key_exists('title', $providerMetadata))
            $this->setName($providerMetadata['title']);
        if (array_key_exists('description', $providerMetadata))
            $this->setDescription($providerMetadata['description']);
        if (array_key_exists('provider_name', $providerMetadata))
            $this->setProviderName($providerMetadata['provider_name']);
        if (array_key_exists('thumbnail_url', $providerMetadata))
            $this->setThumbUrl($providerMetadata['thumbnail_url']);
        if (array_key_exists('thumbnail_width', $providerMetadata))
            $this->setThumbWidth($providerMetadata['thumbnail_width']);
        if (array_key_exists('thumbnail_height', $providerMetadata))
            $this->setThumbHeight($providerMetadata['thumbnail_height']);
    
        return $this;
    }


    /**
     * Get providerMetadata
     *
     * @return array 
     */
    public function getProviderMetadata()
    {
        return $this->providerMetadata;
    }

    /**
     * Set providerVideoId
     *
     * @param string $providerVideoId
     * @return ProjectVideo
     */
    public function setProviderVideoId($providerVideoId)
    {
        $this->providerVideoId = $providerVideoId;
    
        return $this;
    }

    /**
     * Get providerVideoId
     *
     * @return string 
     */
    public function getProviderVideoId()
    {
        return $this->providerVideoId;
    }

    /**
     * Set width
     *
     * @param integer $width
     * @return ProjectVideo
     */
    public function setWidth($width)
    {
        $this->width = $width;
    
        return $this;
    }

    /**
     * Get width
     *
     * @return integer 
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set height
     *
     * @param integer $height
     * @return ProjectVideo
     */
    public function setHeight($height)
    {
        $this->height = $height;
    
        return $this;
    }

    /**
     * Get height
     *
     * @return integer 
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Set length
     *
     * @param float $length
     * @return ProjectVideo
     */
    public function setLength($length)
    {
        $this->length = $length;
    
        return $this;
    }

    /**
     * Get length
     *
     * @return float 
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * Set embedPlayerCode
     *
     * @param string $embedPlayerCode
     * @return ProjectVideo
     */
    public function setEmbedPlayerCode($embedPlayerCode)
    {
        $this->embedPlayerCode = $embedPlayerCode;
    
        return $this;
    }

    /**
     * Get embedPlayerCode
     *
     * @return string 
     */
    public function getEmbedPlayerCode()
    {
        return $this->embedPlayerCode;
    }

    /**
     * Set views
     *
     * @param integer $views
     * @return ProjectVideo
     */
    public function setViews($views)
    {
        $this->views = $views;
    
        return $this;
    }

    /**
     * Get views
     *
     * @return integer 
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * Set thumbWidth
     *
     * @param integer $thumbWidth
     * @return ProjectVideo
     */
    public function setThumbWidth($thumbWidth)
    {
        $this->thumbWidth = $thumbWidth;
    
        return $this;
    }

    /**
     * Get thumbWidth
     *
     * @return integer 
     */
    public function getThumbWidth()
    {
        return $this->thumbWidth;
    }

    /**
     * Set thumbHeight
     *
     * @param integer $thumbHeight
     * @return ProjectVideo
     */
    public function setThumbHeight($thumbHeight)
    {
        $this->thumbHeight = $thumbHeight;
    
        return $this;
    }

    /**
     * Get thumbHeight
     *
     * @return integer 
     */
    public function getThumbHeight()
    {
        return $this->thumbHeight;
    }

    /**
     * Set thumbUrl
     *
     * @param string $thumbUrl
     * @return ProjectVideo
     */
    public function setThumbUrl($thumbUrl)
    {
        $this->thumbUrl = $thumbUrl;
    
        return $this;
    }

    /**
     * Get thumbUrl
     *
     * @return string 
     */
    public function getThumbUrl()
    {
        return $this->thumbUrl;
    }

    /**
     * Set visible
     *
     * @param boolean $visible
     * @return ProjectVideo
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;
    
        return $this;
    }

    /**
     * Get visible
     *
     * @return boolean 
     */
    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * Set hide
     *
     * @param boolean $hide
     * @return ProjectVideo
     */
    public function setHide($hide)
    {
        $this->hide = $hide;
    
        return $this;
    }

    /**
     * Get hide
     *
     * @return boolean 
     */
    public function getHide()
    {
        return $this->hide;
    }

    /**
     * Set highlighted
     *
     * @param boolean $highlighted
     * @return ProjectVideo
     */
    public function setHighlighted($highlighted)
    {
        $this->highlighted = $highlighted;
    
        return $this;
    }

    /**
     * Get highlighted
     *
     * @return boolean 
     */
    public function getHighlighted()
    {
        return $this->highlighted;
    }

    /**
     * Set waitingValidation
     *
     * @param boolean $waitingValidation
     * @return ProjectVideo
     */
    public function setWaitingValidation($waitingValidation)
    {
        $this->waitingValidation = $waitingValidation;
    
        return $this;
    }

    /**
     * Get waitingValidation
     *
     * @return boolean 
     */
    public function getWaitingValidation()
    {
        return $this->waitingValidation;
    }

    /**
     * Set project
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\Project $project
     * @return ProjectVideo
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
     * Set product
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\Project $product
     * @return ProjectVideo
     */
    public function setProduct(\LittleBigJoe\Bundle\CoreBundle\Entity\ProjectProduct $product = null)
    {
        $this->product = $product;
    
        return $this;
    }

    /**
     * Get product
     *
     * @return \LittleBigJoe\Bundle\CoreBundle\Entity\Project 
     */
    public function getProduct()
    {
        return $this->product;
    }
}