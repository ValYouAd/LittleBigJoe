<?php

namespace LittleBigJoe\Bundle\CoreBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Project
 *
 * @ORM\Table(name="brand")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity("slug")
 * @ORM\Entity(repositoryClass="LittleBigJoe\Bundle\CoreBundle\Repository\BrandRepository")
 * @Gedmo\Uploadable(path="uploads/brands", filenameGenerator="SHA1", allowOverwrite=true, appendNumber=true, allowedTypes="image/png,image/jpg,image/jpeg,image/gif")
 */
class Brand
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
     *
     * @Assert\NotBlank(message = "You must enter your brand name")
     * @Assert\Length(
     *    min = "2",
     *    max = "250",
     *    minMessage = "Your brand name must contains at least {{ limit }} characters",
     *    maxMessage = "Your brand name can't exceed {{ limit }} characters"
     * )
     * @Assert\Regex(
     *    pattern = "/^[ÀÁÅÃÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿa-zA-Z -]*$/",
     *    message = "Your brand name must only contains letters, spaces, or dashes"
     * )
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255)
     * @Gedmo\Slug(fields={"name"})
     */
    protected $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="logo", type="string", length=255, nullable=true)
     * @Gedmo\UploadableFilePath
     */
    protected $logo;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     *
     * @Assert\NotBlank(message = "You must enter the description")
     */
    protected $description;

    /**
     * @var string
     *
     * @ORM\Column(name="facebook_url", type="string", length=255, nullable=true)
     *
     * @Assert\Url(message = "Your Facebook URL is invalid")
     */
    protected $facebookUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="twitter_url", type="string", length=255, nullable=true)
     *
     * @Assert\Url(message = "Your Twitter URL is invalid")
     */
    protected $twitterUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="google_url", type="string", length=255, nullable=true)
     *
     * @Assert\Url(message = "Your Google+ URL is invalid")
     */
    protected $googleUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="website_url", type="string", length=255, nullable=true)
     *
     * @Assert\Url(message = "Your website URL is invalid")
     */
    protected $websiteUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="contact_name", type="string", length=255, nullable=true)
     *
     * @Assert\Length(
     *    min = "2",
     *    max = "250",
     *    minMessage = "Your contact name must contains at least {{ limit }} characters",
     *    maxMessage = "Your contact name can't exceed {{ limit }} characters"
     * )
     * @Assert\Regex(
     *    pattern = "/^[ÀÁÅÃÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿa-zA-Z -]*$/",
     *    message = "Your contact name must only contains letters, spaces, or dashes"
     * )
     */
    protected $contactName;

    /**
     * @var string
     *
     * @ORM\Column(name="contact_status", type="string", length=255, nullable=true)
     *
     * @Assert\Length(
     *    min = "2",
     *    max = "250",
     *    minMessage = "Your contact status must contains at least {{ limit }} characters",
     *    maxMessage = "Your contact status can't exceed {{ limit }} characters"
     * )
     * @Assert\Regex(
     *    pattern = "/^[ÀÁÅÃÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿa-zA-Z -]*$/",
     *    message = "Your contact status must only contains letters, spaces, or dashes"
     * )
     */
    protected $contactStatus;

    /**
     * @var string
     *
     * @ORM\Column(name="contact_phone", type="string", length=30, nullable=true)
     *
     * @Assert\Regex(
     *    pattern = "/^[0-9\.\-\,]*$/",
     *    message = "Your contact phone must only contains numbers, dots, or commas"
     * )
     */
    protected $contactPhone;

    /**
     * @var string
     *
     * @ORM\Column(name="contact_email", type="string", length=255, nullable=true)
     *
     * @Assert\Email(
     *     message = "Your contact email '{{ value }}' is not a valid email."
     * )
     */
    protected $contactEmail;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="minimum_likes_required", type="integer", nullable=true)
     *
     * @Assert\NotBlank(message = "You must enter the minimum required likes count")
     * @Assert\Regex(
     *    pattern = "/^[0-9]*$/",
     *    message = "Your minimum required likes count must only contains numbers"
     * )
     */
    protected $minimumLikesRequired = 0;

    /**
     * @ORM\OneToMany(targetEntity="Project", mappedBy="brand", cascade={"persist", "remove"})
     */
    protected $projects;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="followedBrands")
     */
    protected $followers;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="brands")
     */
    protected $administrators;
    
    public function __construct()
    {
        $this->projects = new ArrayCollection();
        $this->followers = new ArrayCollection();
        $this->administrators = new ArrayCollection();
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
     * @return Brand
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
     * @return Brand
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
     * Set logo
     *
     * @param string $logo
     * @return Brand
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * Get logo
     *
     * @return string
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Brand
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
     * Set facebookUrl
     *
     * @param string $facebookUrl
     * @return Brand
     */
    public function setFacebookUrl($facebookUrl)
    {
        $this->facebookUrl = $facebookUrl;

        return $this;
    }

    /**
     * Get facebookUrl
     *
     * @return string
     */
    public function getFacebookUrl()
    {
        return $this->facebookUrl;
    }

    /**
     * Set twitterUrl
     *
     * @param string $twitterUrl
     * @return Brand
     */
    public function setTwitterUrl($twitterUrl)
    {
        $this->twitterUrl = $twitterUrl;

        return $this;
    }

    /**
     * Get twitterUrl
     *
     * @return string
     */
    public function getTwitterUrl()
    {
        return $this->twitterUrl;
    }

    /**
     * Set googleUrl
     *
     * @param string $googleUrl
     * @return Brand
     */
    public function setGoogleUrl($googleUrl)
    {
        $this->googleUrl = $googleUrl;

        return $this;
    }

    /**
     * Get googleUrl
     *
     * @return string
     */
    public function getGoogleUrl()
    {
        return $this->googleUrl;
    }

    /**
     * Set websiteUrl
     *
     * @param string $websiteUrl
     * @return Brand
     */
    public function setWebsiteUrl($websiteUrl)
    {
        $this->websiteUrl = $websiteUrl;

        return $this;
    }

    /**
     * Get websiteUrl
     *
     * @return string
     */
    public function getWebsiteUrl()
    {
        return $this->websiteUrl;
    }

    /**
     * Set contactName
     *
     * @param string $contactName
     * @return Brand
     */
    public function setContactName($contactName)
    {
        $this->contactName = $contactName;

        return $this;
    }

    /**
     * Get contactName
     *
     * @return string
     */
    public function getContactName()
    {
        return $this->contactName;
    }

    /**
     * Set contactStatus
     *
     * @param string $contactStatus
     * @return Brand
     */
    public function setContactStatus($contactStatus)
    {
        $this->contactStatus = $contactStatus;

        return $this;
    }

    /**
     * Get contactStatus
     *
     * @return string
     */
    public function getContactStatus()
    {
        return $this->contactStatus;
    }

    /**
     * Set contactPhone
     *
     * @param string $contactPhone
     * @return Brand
     */
    public function setContactPhone($contactPhone)
    {
        $this->contactPhone = $contactPhone;

        return $this;
    }

    /**
     * Get contactPhone
     *
     * @return string
     */
    public function getContactPhone()
    {
        return $this->contactPhone;
    }

    /**
     * Set contactEmail
     *
     * @param string $contactEmail
     * @return Brand
     */
    public function setContactEmail($contactEmail)
    {
        $this->contactEmail = $contactEmail;

        return $this;
    }

    /**
     * Get contactEmail
     *
     * @return string
     */
    public function getContactEmail()
    {
        return $this->contactEmail;
    }

    /**
     * Set minimumLikesRequired
     *
     * @param integer $minimumLikesRequired
     * @return Brand
     */
    public function setMinimumLikesRequired($minimumLikesRequired)
    {
	    	$this->minimumLikesRequired = $minimumLikesRequired;
	    
	    	return $this;
    }
    
    /**
     * Get minimumLikesRequired
     *
     * @return integer
     */
    public function getMinimumLikesRequired()
    {
    		return $this->minimumLikesRequired;
    }
    
    /**
     * Add projects
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\Project $projects
     * @return Brand
     */
    public function addProject(\LittleBigJoe\Bundle\CoreBundle\Entity\Project $projects)
    {
        $this->projects[] = $projects;

        return $this;
    }

    /**
     * Remove projects
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\Project $projects
     */
    public function removeProject(\LittleBigJoe\Bundle\CoreBundle\Entity\Project $projects)
    {
        $this->projects->removeElement($projects);
    }

    /**
     * Get projects
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProjects()
    {
        return $this->projects;
    }

    /**
     * Add followers
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\User $followers
     * @return Brand
     */
    public function addFollower(\LittleBigJoe\Bundle\CoreBundle\Entity\User $followers)
    {
        $this->followers[] = $followers;
    
        return $this;
    }

    /**
     * Remove followers
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\User $followers
     */
    public function removeFollower(\LittleBigJoe\Bundle\CoreBundle\Entity\User $followers)
    {
        $this->followers->removeElement($followers);
    }

    /**
     * Get followers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFollowers()
    {
        return $this->followers;
    }

    /**
     * Add administrators
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\User $administrators
     * @return Brand
     */
    public function addAdministrator(\LittleBigJoe\Bundle\CoreBundle\Entity\User $administrators)
    {
        $this->administrators[] = $administrators;
    
        return $this;
    }

    /**
     * Remove administrators
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\User $administrators
     */
    public function removeAdministrator(\LittleBigJoe\Bundle\CoreBundle\Entity\User $administrators)
    {
        $this->administrators->removeElement($administrators);
    }

    /**
     * Get administrators
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAdministrators()
    {
        return $this->administrators;
    }
}