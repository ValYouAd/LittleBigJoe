<?php

namespace LittleBigJoe\Bundle\CoreBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Locale\Locale;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity("email")
 * @ORM\Entity(repositoryClass="LittleBigJoe\Bundle\CoreBundle\Repository\UserRepository")
 * @Gedmo\Uploadable(path="uploads/users", filenameGenerator="SHA1", allowOverwrite=true, appendNumber=true, allowedTypes="image/png,image/jpg,image/jpeg,image/gif")
 */
class User extends BaseUser
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
     * @ORM\Column(name="gender", type="integer", nullable=true)
     */
    protected $gender = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=255)
     *
     * @Assert\NotBlank(message = "You must enter your firstname")
     * @Assert\Length(
     *    min = "2",
     *    max = "250",
     *    minMessage = "Your firstname must contains at least {{ limit }} characters",
     *    maxMessage = "Your firstname can't exceed {{ limit }} characters"
     * )
     * @Assert\Regex(
     *    pattern = "/^[ÀÁÅÃÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿa-zA-Z -]*$/",
     *    message = "Your firstname must only contains letters, spaces, or dashes"
     * )
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=255)
     *
     * @Assert\NotBlank(message = "You must enter your lastname")
     * @Assert\Length(
     *    min = "2",
     *    max = "250",
     *    minMessage = "Your lastname must contains at least {{ limit }} characters",
     *    maxMessage = "Your lastname can't exceed {{ limit }} characters"
     * )
     * @Assert\Regex(
     *    pattern = "/^[ÀÁÅÃÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿa-zA-Z -]*$/",
     *    message = "Your lastname must only contains letters, spaces, or dashes"
     * )
     */
    private $lastname;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="birthday", type="datetime")
     *
     * @Assert\NotBlank(message = "You must enter your birthday")
     */
    private $birthday;

    /**
     * @var string
     *
     * @ORM\Column(name="facebook_id", type="string", length=255, nullable=true)
     */
    private $facebookId;
    
    /**
     * @var string
     *
     * @ORM\Column(name="facebook_url", type="string", length=255, nullable=true)
     *
     * @Assert\Url(message = "Your Facebook URL is invalid")
     */
    private $facebookUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="twitter_id", type="string", length=255, nullable=true)
     */
    private $twitterId;
    
    /**
     * @var string
     *
     * @ORM\Column(name="twitter_access_token", type="string", length=255, nullable=true)
     */
    private $twitterAccessToken;
    
    /**
     * @var string
     *
     * @ORM\Column(name="twitter_access_token_secret", type="string", length=255, nullable=true)
     */
    private $twitterAccessTokenSecret;
    
    /**
     * @var string
     *
     * @ORM\Column(name="twitter_url", type="string", length=255, nullable=true)
     *
     * @Assert\Url(message = "Your Twitter URL is invalid")
     */
    private $twitterUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="google_url", type="string", length=255, nullable=true)
     *
     * @Assert\Url(message = "Your Google+ URL is invalid")
     */
    private $googleUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="website_url", type="string", length=255, nullable=true)
     */
    private $websiteUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=255, nullable=true)
     *
     * @Assert\Length(
     *    min = "2",
     *    max = "250",
     *    minMessage = "Your city must contains at least {{ limit }} characters",
     *    maxMessage = "Your city can't exceed {{ limit }} characters"
     * )
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=255, nullable=true)
     *
     * @Assert\Country(message = "Your country name is invalid")
     */
    private $country;

    /**
     * @var string
     *
     * @ORM\Column(name="default_language", type="string", length=45, nullable=true)
     *
     * @Assert\Language(message = "The language is incorrect")
     * @Assert\Choice(choices = {"en", "fr"}, message = "Choose a valid language.")
     */
    private $defaultLanguage;

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
     * @ORM\Column(name="bio", type="text", nullable=true)
     * @Assert\Length(
     *    min = "2",
     *    max = "300",
     *    minMessage = "Your biography must contains at least {{ limit }} characters",
     *    maxMessage = "Your biography can't exceed {{ limit }} characters"
     * )
     */
    private $bio;

    /**
     * @var string
     *
     * @ORM\Column(name="ip_address", type="string", length=50, nullable=true)
     */
    private $ipAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="person_type", type="string", length=100)
     */
    private $personType;

    /**
     * @var integer
     *
     * @ORM\Column(name="mangopay_user_id", type="integer")
     */
    private $mangopayUserId;

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
     * @ORM\OneToMany(targetEntity="Project", mappedBy="user", cascade={"persist", "remove"})
     */
    protected $projects;

    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="user", cascade={"persist", "remove"})
     */
    protected $comments;
    
    /**
     * @ORM\OneToMany(targetEntity="EntryComment", mappedBy="user", cascade={"persist", "remove"})
     */
    protected $entry_comments;

    /**
     * @ORM\OneToMany(targetEntity="ProjectLike", mappedBy="user", cascade={"persist", "remove"})
     */
    protected $likes;

    /**
     * @ORM\OneToMany(targetEntity="ProjectContribution", mappedBy="user", cascade={"persist", "remove"})
     */
    protected $contributions;
    
    /**
     * @ORM\OneToMany(targetEntity="Beneficiary", mappedBy="user", cascade={"persist", "remove"})
     */
    protected $beneficiaries;
    
    /**
     * @ORM\OneToMany(targetEntity="Withdrawal", mappedBy="user", cascade={"persist", "remove"})
     */
    protected $withdrawals;
    
    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="followedUsers")
     */
    protected $followers;

    /**
     * @ORM\ManyToMany(targetEntity="User", inversedBy="followers")
     * @ORM\JoinTable(name="users_followers",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="follower_user_id", referencedColumnName="id")}
     *      )
     */
    protected $followedUsers;

    /**
     * @ORM\ManyToMany(targetEntity="Brand", inversedBy="followers")
     * @ORM\JoinTable(name="users_brands")
     */
    protected $followedBrands;
    
    /**
     * @ORM\OneToMany(targetEntity="Notification", mappedBy="user", cascade={"persist", "remove"})
     */
    protected $notifications;

    /**
     * @ORM\OneToMany(targetEntity="ProjectHelp", mappedBy="project", cascade={"persist", "remove"})
     */
    protected $projectHelps;

    /**
     * @ORM\ManyToMany(targetEntity="Brand", cascade={"persist"}, inversedBy="administrators")
     * @ORM\JoinTable(name="user_brands")
     */
    protected $brands;

    /**
     * @ORM\OneToMany(targetEntity="ProjectProductComment", mappedBy="user", cascade={"persist", "remove"})
     */
    protected $productComments;

    /**
     * @var string
     * @ORM\Column(name="betaCodeValue", type="string", nullable=true)
     */
    private $betaCodeValue;

    /**
     * @ORM\ManyToOne(targetEntity="Code", inversedBy="users")
     * @ORM\JoinColumn(name = "betaCode_id", referencedColumnName = "id", onDelete="SET NULL")
     */
    protected $betaCode;

    public function __construct()
    {
        parent::__construct();

        $this->enabled = true;
        $this->roles = array('ROLE_USER');
        $this->comments = new ArrayCollection();
        $this->entry_comments = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->contributions = new ArrayCollection();
        $this->beneficiaries = new ArrayCollection();
        $this->withdrawals = new ArrayCollection();
        $this->followers = new ArrayCollection();
        $this->followedUsers = new ArrayCollection();
        $this->followedBrands = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        $this->projectHelps = new ArrayCollection();
        $this->brands = new ArrayCollection();
        $this->productComments = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP']))
            $ipAddress = $_SERVER['HTTP_CLIENT_IP'];
        else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if (!empty($_SERVER['HTTP_X_FORWARDED']))
            $ipAddress = $_SERVER['HTTP_X_FORWARDED'];
        else if (!empty($_SERVER['HTTP_FORWARDED_FOR']))
            $ipAddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if (!empty($_SERVER['HTTP_FORWARDED']))
            $ipAddress = $_SERVER['HTTP_FORWARDED'];
        else
            $ipAddress = $_SERVER['REMOTE_ADDR'];

        $this->personType = 'NATURAL_PERSON';
        $this->ipAddress = $ipAddress;
        $this->mangopayUserId = 0;
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
     * Set gender
     *
     * @param string $gender
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
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     * @return User
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     * @return User
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set birthday
     *
     * @param \DateTime $birthday
     * @return User
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * Get birthday
     *
     * @return \DateTime
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Set facebookId
     *
     * @param string $facebookId
     * @return User
     */
    public function setFacebookId($facebookId)
    {
	    	$this->facebookId = $facebookId;
	    
	    	return $this;
    }
    
    /**
     * Get facebookId
     *
     * @return string
     */
    public function getFacebookId()
    {
    		return $this->facebookId;
    }
    
    /**
     * Fetch infos from Facebook and associate them to current user
     * 
     * @param Array
     */
    public function setFBData($fbdata)
    {
    		if (isset($fbdata['id'])) 
	    	{
		    		$this->setFacebookId($fbdata['id']);
		    		$this->addRole('ROLE_FACEBOOK');
	    	}
	    	if (isset($fbdata['first_name'])) 
	    	{
	    			$this->setFirstname($fbdata['first_name']);
	    	}
	    	if (isset($fbdata['last_name'])) 
	    	{
	    			$this->setLastname($fbdata['last_name']);
	    	}
	    	if (isset($fbdata['email'])) 
	    	{
	    			$this->setEmail($fbdata['email']);
	    			$this->setEmailCanonical($fbdata['email']);
	    	}
	    	if (isset($fbdata['locale']))
	    	{
	    			$locale = substr($fbdata['locale'], 0, 2);
	    			$country = substr($fbdata['locale'], 3, 2);
	    			
	    			// Avoid to select non configured language	    			
	    			if (!in_array($locale, array('en', 'fr')))
	    			{
	    					$locale = 'en';
	    			}
	    			
	    			$this->setDefaultLanguage($locale);
	    			$this->setCountry($country);
	    	}
	    	if (isset($fbdata['link']))
	    	{
	    			$this->setFacebookUrl($fbdata['link']);
	    	}
	    	if (isset($fbdata['bio']))
	    	{
	    			$this->setBio($fbdata['bio']);
	    	}
	    	if (isset($fbdata['birthday']))
	    	{
	    			$this->setBirthday(\DateTime::createFromFormat('m/d/Y', $fbdata['birthday']));
	    	}
	    	if (isset($fbdata['location']))
	    	{
	    			$this->setCity($fbdata['location']['name']);
	    	}
	    	if (isset($fbdata['website']))
	    	{
	    			$this->setWebsiteUrl($fbdata['website']);
	    	}	    	
    }
    
    /**
     * Set facebookUrl
     *
     * @param string $facebookUrl
     * @return User
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
     * Set twitterId
     *
     * @param string $twitterId
     * @return User
     */
    public function setTwitterId($twitterId)
    {
	    	$this->twitterId = $twitterId;
	    
	    	return $this;
    }
    
    /**
     * Get twitterId
     *
     * @return string
     */
    public function getTwitterId()
    {
    		return $this->twitterId;
    }
    
    /**
     * Set twitterAccessToken
     *
     * @param string $twitterAccessToken
     * @return User
     */
    public function setTwitterAccessToken($twitterAccessToken)
    {
	    	$this->twitterAccessToken = $twitterAccessToken;
	    
	    	return $this;
    }
    
    /**
     * Get twitterAccessToken
     *
     * @return string
     */
    public function getTwitterAccessToken()
    {
    		return $this->twitterAccessToken;
    }
    
    /**
     * Set twitterAccessTokenSecret
     *
     * @param string $twitterAccessTokenSecret
     * @return User
     */
    public function setTwitterAccessTokenSecret($twitterAccessTokenSecret)
    {
        $this->twitterAccessTokenSecret = $twitterAccessTokenSecret;
         
        return $this;
    }
    
    /**
     * Get twitterAccessTokenSecret
     *
     * @return string
     */
    public function getTwitterAccessTokenSecret()
    {
        return $this->twitterAccessTokenSecret;
    }
    
    /**
     * Set twitterUrl
     *
     * @param string $twitterUrl
     * @return User
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
     * @return User
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
     * @return User
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
     * Set city
     *
     * @param string $city
     * @return User
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set country
     *
     * @param string $country
     * @return User
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set defaultLanguage
     *
     * @param string $defaultLanguage
     * @return User
     */
    public function setDefaultLanguage($defaultLanguage)
    {
        $this->defaultLanguage = $defaultLanguage;

        return $this;
    }

    /**
     * Get defaultLanguage
     *
     * @return string
     */
    public function getDefaultLanguage()
    {
        return $this->defaultLanguage;
    }

    /**
     * Set photo
     *
     * @param string $photo
     * @return User
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
     * Set bio
     *
     * @param string $bio
     * @return User
     */
    public function setBio($bio)
    {
        $this->bio = strip_tags($bio);

        return $this;
    }

    /**
     * Get bio
     *
     * @return string
     */
    public function getBio()
    {
        return $this->bio;
    }

    /**
     * Set ipAddress
     *
     * @param string $ipAddress
     * @return User
     */
    public function setIpAddress($ipAddress)
    {
        $this->ipAddress = $ipAddress;

        return $this;
    }

    /**
     * Get ipAddress
     *
     * @return string
     */
    public function getIpAddress()
    {
        return $this->ipAddress;
    }

    /**
     * Set personType
     *
     * @param string $personType
     * @return User
     */
    public function setPersonType($personType)
    {
        $this->personType = $personType;

        return $this;
    }

    /**
     * Get personType
     *
     * @return string
     */
    public function getPersonType()
    {
        return $this->personType;
    }

    /**
     * Set mangopayUserId
     *
     * @param integer $mangopayUserId
     * @return User
     */
    public function setMangopayUserId($mangopayUserId)
    {
        $this->mangopayUserId = $mangopayUserId;

        return $this;
    }

    /**
     * Get mangopayUserId
     *
     * @return integer
     */
    public function getMangopayUserId()
    {
        return $this->mangopayUserId;
    }

    /**
     * Set mangopayCreatedAt
     *
     * @param \DateTime $mangopayCreatedAt
     * @return User
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
     * @return User
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
     * Add comments
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\EntryComment $comments
     * @return User
     */
    public function addComment(\LittleBigJoe\Bundle\CoreBundle\Entity\EntryComment $comments)
    {
        $this->comments[] = $comments;

        return $this;
    }

    /**
     * Remove comments
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\EntryComment $comments
     */
    public function removeComment(\LittleBigJoe\Bundle\CoreBundle\Entity\EntryComment $comments)
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
     * Add likes
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\ProjectLike $likes
     * @return User
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
     * Add contributions
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\ProjectContribution $contributions
     * @return User
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
     * Add projects
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\Project $projects
     * @return User
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

    public function setEmail($email)
    {
        $this->email = $email;
        $this->username = $email;
    }

    public function setEmailCanonical($emailCanonical)
    {
        $this->emailCanonical = $emailCanonical;
        $this->usernameCanonical = $emailCanonical;
    }

    /**
     * Add entry_comments
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\EntryComment $entryComments
     * @return User
     */
    public function addEntryComment(\LittleBigJoe\Bundle\CoreBundle\Entity\EntryComment $entryComments)
    {
        $this->entry_comments[] = $entryComments;
    
        return $this;
    }

    /**
     * Remove entry_comments
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\EntryComment $entryComments
     */
    public function removeEntryComment(\LittleBigJoe\Bundle\CoreBundle\Entity\EntryComment $entryComments)
    {
        $this->entry_comments->removeElement($entryComments);
    }

    /**
     * Get entry_comments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEntryComments()
    {
        return $this->entry_comments;
    }

    /**
     * Add beneficiaries
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\Beneficiary $beneficiaries
     * @return User
     */
    public function addBeneficiarie(\LittleBigJoe\Bundle\CoreBundle\Entity\Beneficiary $beneficiaries)
    {
        $this->beneficiaries[] = $beneficiaries;
    
        return $this;
    }

    /**
     * Remove beneficiaries
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\Beneficiary $beneficiaries
     */
    public function removeBeneficiarie(\LittleBigJoe\Bundle\CoreBundle\Entity\Beneficiary $beneficiaries)
    {
        $this->beneficiaries->removeElement($beneficiaries);
    }

    /**
     * Get beneficiaries
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getBeneficiaries()
    {
        return $this->beneficiaries;
    }

    /**
     * Add withdrawals
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\Withdrawal $withdrawals
     * @return User
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

    /**
     * Add followers
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\User $followers
     * @return User
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
     * Add followedUsers
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\User $followedUsers
     * @return User
     */
    public function addFollowedUser(\LittleBigJoe\Bundle\CoreBundle\Entity\User $followedUsers)
    {
        $this->followedUsers[] = $followedUsers;
    
        return $this;
    }

    /**
     * Remove followedUsers
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\User $followedUsers
     */
    public function removeFollowedUser(\LittleBigJoe\Bundle\CoreBundle\Entity\User $followedUsers)
    {
        $this->followedUsers->removeElement($followedUsers);
    }

    /**
     * Get followedUsers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFollowedUsers()
    {
        return $this->followedUsers;
    }

    /**
     * Add followedBrands
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\Brand $followedBrands
     * @return User
     */
    public function addFollowedBrand(\LittleBigJoe\Bundle\CoreBundle\Entity\Brand $followedBrands)
    {
        $this->followedBrands[] = $followedBrands;
    
        return $this;
    }

    /**
     * Remove followedBrands
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\Brand $followedBrands
     */
    public function removeFollowedBrand(\LittleBigJoe\Bundle\CoreBundle\Entity\Brand $followedBrands)
    {
        $this->followedBrands->removeElement($followedBrands);
    }

    /**
     * Get followedBrands
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFollowedBrands()
    {
        return $this->followedBrands;
    }

    /**
     * Add notifications
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\Notification $notifications
     * @return User
     */
    public function addNotification(\LittleBigJoe\Bundle\CoreBundle\Entity\Notification $notifications)
    {
        $this->notifications[] = $notifications;
    
        return $this;
    }

    /**
     * Remove notifications
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\Notification $notifications
     */
    public function removeNotification(\LittleBigJoe\Bundle\CoreBundle\Entity\Notification $notifications)
    {
        $this->notifications->removeElement($notifications);
    }

    /**
     * Get notifications
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getNotifications()
    {
        return $this->notifications;
    }

    /**
     * Add projectHelps
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\ProjectHelp $projectHelps
     * @return User
     */
    public function addProjectHelp(\LittleBigJoe\Bundle\CoreBundle\Entity\ProjectHelp $projectHelps)
    {
        $this->projectHelps[] = $projectHelps;
    
        return $this;
    }

    /**
     * Remove projectHelps
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\ProjectHelp $projectHelps
     */
    public function removeProjectHelp(\LittleBigJoe\Bundle\CoreBundle\Entity\ProjectHelp $projectHelps)
    {
        $this->projectHelps->removeElement($projectHelps);
    }

    /**
     * Get projectHelps
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProjectHelps()
    {
        return $this->projectHelps;
    }

    /**
     * Add brands
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\Brand $brands
     * @return User
     */
    public function addBrand(\LittleBigJoe\Bundle\CoreBundle\Entity\Brand $brands)
    {
        $this->brands[] = $brands;
    
        return $this;
    }

    /**
     * Remove brands
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\Brand $brands
     */
    public function removeBrand(\LittleBigJoe\Bundle\CoreBundle\Entity\Brand $brands)
    {
        $this->brands->removeElement($brands);
    }

    /**
     * Get brands
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getBrands()
    {
        return $this->brands;
    }

    /**
     * Add productComments
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\ProjectProductComment $productComments
     * @return User
     */
    public function addProductComment(\LittleBigJoe\Bundle\CoreBundle\Entity\ProjectProductComment $productComments)
    {
        $this->productComments[] = $productComments;
    
        return $this;
    }

    /**
     * Remove productComments
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\ProjectProductComment $productComments
     */
    public function removeProductComment(\LittleBigJoe\Bundle\CoreBundle\Entity\ProjectProductComment $productComments)
    {
        $this->productComments->removeElement($productComments);
    }

    /**
     * Get productComments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProductComments()
    {
        return $this->productComments;
    }

    /**
     * Set betaCodeValue
     *
     * @param string $betaCodeValue
     * @return User
     */
    public function setBetaCodeValue($betaCodeValue)
    {
        $this->betaCodeValue = $betaCodeValue;
    
        return $this;
    }

    /**
     * Get betaCodeValue
     *
     * @return string 
     */
    public function getBetaCodeValue()
    {
        return $this->betaCodeValue;
    }

    /**
     * Set betaCode
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\Code $betaCode
     * @return User
     */
    public function setBetaCode(\LittleBigJoe\Bundle\CoreBundle\Entity\Code $betaCode = null)
    {
        $this->betaCode = $betaCode;
    
        return $this;
    }

    /**
     * Get betaCode
     *
     * @return \LittleBigJoe\Bundle\CoreBundle\Entity\Code 
     */
    public function getBetaCode()
    {
        return $this->betaCode;
    }

    public function getAvailableProjects()
    {
        $nb = 0;
        if (sizeof($this->projects) > 0)
        {
            foreach ($this->projects as $project)
            {
                if ($project->getDeletedAt() == null)
                {
                    $nb++;
                }
            }
        }

        return $nb;
    }
}
