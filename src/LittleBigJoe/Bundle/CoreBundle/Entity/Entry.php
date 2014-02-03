<?php

namespace LittleBigJoe\Bundle\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Entry
 *
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="entry")
 * @ORM\Entity(repositoryClass="LittleBigJoe\Bundle\CoreBundle\Repository\EntryRepository")
 */
class Entry
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
     * @ORM\Column(name="title", type="string", length=255)
     *
     * @Assert\NotBlank(message = "You must enter your entry title")
     * @Assert\Length(
     *    min = "2",
     *    max = "250",
     *    minMessage = "Your entry title must contains at least {{ limit }} characters",
     *    maxMessage = "Your entry title can't exceed {{ limit }} characters"
     * )
     * @Assert\Regex(
     *    pattern = "/^[ÀÁÅÃÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿa-zA-Z -]*$/",
     *    message = "Your entry title must only contains letters, spaces, or dashes"
     * )
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     *
     * @Assert\NotBlank(message = "You must enter your entry content")
     */
    private $content;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_public", type="boolean")
     */
    private $isPublic;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true)
     */
    private $deletedAt;

    /**
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="entries")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     */
    protected $project;

    /**
     * @ORM\OneToMany(targetEntity="EntryComment", mappedBy="entry", cascade={"persist", "remove"})
     */
    protected $comments;

    /**
     * @ORM\OneToMany(targetEntity="Notification", mappedBy="entry", cascade={"persist", "remove"})
     */
    protected $notifications;
    
    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->notifications = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->title;
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
     * Set title
     *
     * @param string $title
     * @return Entry
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return Entry
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return html_entity_decode($this->content);
    }

    /**
     * Set isPublic
     *
     * @param boolean $isPublic
     * @return Entry
     */
    public function setIsPublic($isPublic)
    {
        $this->isPublic = $isPublic;

        return $this;
    }

    /**
     * Get isPublic
     *
     * @return boolean
     */
    public function getIsPublic()
    {
        return $this->isPublic;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Entry
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
     * Set deletedAt
     *
     * @param \DateTime $deletedAt
     * @return Entry
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
     * Set project
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\Project $project
     * @return Entry
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
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\EntryComment $comments
     * @return Entry
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
     * Add notifications
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\Notification $notifications
     * @return Entry
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
}