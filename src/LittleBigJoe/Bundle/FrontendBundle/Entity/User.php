<?php

namespace LittleBigJoe\Bundle\FrontendBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity("email")
 * @ORM\Entity(repositoryClass="LittleBigJoe\Bundle\FrontendBundle\Repository\UserRepository")
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
     * @Assert\NotBlank(message = "You must enter your lastname")
     * @Assert\DateTime(message = "Your birthday date format is incorrect")
     */
    private $birthday;

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
     *
     * @Assert\Url(message = "Your website URL is invalid")
     */
    private $websiteUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=255)
     *
     * @Assert\NotBlank(message = "You must enter your city")
     * @Assert\Length(
     *    min = "2",
     *    max = "250",
     *    minMessage = "Your city must contains at least {{ limit }} characters",
     *    maxMessage = "Your city can't exceed {{ limit }} characters"
     * )
     * @Assert\Regex(
     *        pattern = "/^[ÀÁÅÃÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿa-zA-Z -]*$/",
     *    message = "Your city must only contains letters, spaces, or dashes"
     * )
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=255)
     *
     * @Assert\NotBlank(message = "You must enter your country")
     * @Assert\Country(message = "Your country name is invalid")
     */
    private $country;

    /**
     * @var string
     *
     * @ORM\Column(name="nationality", type="string", length=255)
     *
     * @Assert\NotBlank(message = "You must enter your nationality")
     * @Assert\Length(
     *    min = "2",
     *    max = "250",
     *    minMessage = "Your nationality must contains at least {{ limit }} characters",
     *    maxMessage = "Your nationality can't exceed {{ limit }} characters"
     * )
     * @Assert\Regex(
     *    pattern = "/^[ÀÁÅÃÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿa-zA-Z -]*$/",
     *    message = "Your nationality must only contains letters, spaces, or dashes"
     * )
     */
    private $nationality;

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
     *
     * @Assert\Regex(
     *    pattern = "/^[ÀÁÅÃÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿa-zA-Z0-9 \-\(\)\[\]\.\,\:\;\!]*$/",
     *    message = "Your bio must only contains numbers, letters, spaces, dots, commas, exclamation marks or dashes"
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
     * @ORM\OneToMany(targetEntity="EntryComment", mappedBy="user", cascade={"persist", "remove"})
     */
    protected $comments;

    /**
     * @ORM\OneToMany(targetEntity="ProjectLike", mappedBy="user", cascade={"persist", "remove"})
     */
    protected $likes;

    /**
     * @ORM\OneToMany(targetEntity="ProjectContribution", mappedBy="user", cascade={"persist", "remove"})
     */
    protected $contributions;

    public function __construct()
    {
        parent::__construct();

        $this->enabled = true;
        $this->roles = array('ROLE_USER');
        $this->comments = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->contributions = new ArrayCollection();
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
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
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
     * Set nationality
     *
     * @param string $nationality
     * @return User
     */
    public function setNationality($nationality)
    {
        $this->nationality = $nationality;

        return $this;
    }

    /**
     * Get nationality
     *
     * @return string
     */
    public function getNationality()
    {
        return $this->nationality;
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
     * @param \LittleBigJoe\Bundle\FrontendBundle\Entity\EntryComment $comments
     * @return User
     */
    public function addComment(\LittleBigJoe\Bundle\FrontendBundle\Entity\EntryComment $comments)
    {
        $this->comments[] = $comments;

        return $this;
    }

    /**
     * Remove comments
     *
     * @param \LittleBigJoe\Bundle\FrontendBundle\Entity\EntryComment $comments
     */
    public function removeComment(\LittleBigJoe\Bundle\FrontendBundle\Entity\EntryComment $comments)
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
     * @param \LittleBigJoe\Bundle\FrontendBundle\Entity\ProjectLike $likes
     * @return User
     */
    public function addLike(\LittleBigJoe\Bundle\FrontendBundle\Entity\ProjectLike $likes)
    {
        $this->likes[] = $likes;

        return $this;
    }

    /**
     * Remove likes
     *
     * @param \LittleBigJoe\Bundle\FrontendBundle\Entity\ProjectLike $likes
     */
    public function removeLike(\LittleBigJoe\Bundle\FrontendBundle\Entity\ProjectLike $likes)
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
     * @param \LittleBigJoe\Bundle\FrontendBundle\Entity\ProjectContribution $contributions
     * @return User
     */
    public function addContribution(\LittleBigJoe\Bundle\FrontendBundle\Entity\ProjectContribution $contributions)
    {
        $this->contributions[] = $contributions;

        return $this;
    }

    /**
     * Remove contributions
     *
     * @param \LittleBigJoe\Bundle\FrontendBundle\Entity\ProjectContribution $contributions
     */
    public function removeContribution(\LittleBigJoe\Bundle\FrontendBundle\Entity\ProjectContribution $contributions)
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
     * @param \LittleBigJoe\Bundle\FrontendBundle\Entity\Project $projects
     * @return User
     */
    public function addProject(\LittleBigJoe\Bundle\FrontendBundle\Entity\Project $projects)
    {
        $this->projects[] = $projects;

        return $this;
    }

    /**
     * Remove projects
     *
     * @param \LittleBigJoe\Bundle\FrontendBundle\Entity\Project $projects
     */
    public function removeProject(\LittleBigJoe\Bundle\FrontendBundle\Entity\Project $projects)
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
}