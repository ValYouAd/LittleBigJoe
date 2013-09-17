<?php

namespace LittleBigJoe\Bundle\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * ProjectReward
 *
 * @ORM\Table(name="project_reward")
 * @ORM\Entity(repositoryClass="LittleBigJoe\Bundle\CoreBundle\Repository\ProjectRewardRepository")
 */
class ProjectReward
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
     * @Assert\NotBlank(message = "You must enter the reward title", groups = {"Default", "flow_createProject_step4"})
     * @Assert\Length(
     *    min = "2",
     *    max = "250",
     *    minMessage = "Your reward title must contains at least {{ limit }} characters",
     *    maxMessage = "Your reward title can't exceed {{ limit }} characters", 
     *    groups = {"Default", "flow_createProject_step4"}
     * )
     * @Assert\Regex(
     *    pattern = "/^[ÀÁÅÃÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿa-zA-Z0-9 -]*$/",
     *    message = "Your reward title must only contains letters, spaces, or dashes", 
     *    groups = {"Default", "flow_createProject_step4"}
     * )
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     *
     * @Assert\NotBlank(message = "You must enter the reward description", groups = {"Default", "flow_createProject_step4"})
     * @Assert\Regex(
     *    pattern = "/^[ÀÁÅÃÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿa-zA-Z0-9 \-\(\)\[\]\.\,\:\;\!]*$/",
     *    message = "Your description must only contains numbers, letters, spaces, dots, commas, exclamation marks or dashes", 
     *    groups = {"Default", "flow_createProject_step4"}
     * )
     */
    private $description;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="decimal")
     *
     * @Assert\NotBlank(message = "You must enter the reward", groups = {"Default", "flow_createProject_step4"})
     * @Assert\Regex(
     *    pattern = "/^[0-9\.\,]*$/",
     *    message = "Your reward amount must only contains numbers, dots, or commas", 
     *    groups = {"Default", "flow_createProject_step4"}
     * )
     */
    private $amount;

    /**
     * @var integer
     *
     * @ORM\Column(name="stock", type="integer")
     *
     * @Assert\NotBlank(message = "You must enter the stock", groups = {"Default", "flow_createProject_step4"})
     * @Assert\Regex(
     *    pattern = "/^[0-9]*$/",
     *    message = "Your stock must only contains numbers", 
     *    groups = {"Default", "flow_createProject_step4"}
     * )
     */
    private $stock;

    /**
     * @var integer
     *
     * @ORM\Column(name="max_quantity_by_user", type="integer")
     * @Assert\NotBlank(message = "You must enter the max quantity by user", groups = {"Default", "flow_createProject_step4"})
     * @Assert\Regex(
     *    pattern = "/^[0-9]*$/",
     *    message = "Your max quantity by user must only contains numbers", 
     *    groups = {"Default", "flow_createProject_step4"}
     * )
     */
    private $maxQuantityByUser;

    /**
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="rewards", cascade={"persist"})
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     */
    protected $project;
        
    /**
     * @ORM\OneToMany(targetEntity="ProjectContribution", mappedBy="reward", cascade={"persist", "remove"})
     */
    protected $contributions;

    public function __construct()
    {
        $this->contributions = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->title;
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
     * @return Reward
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
     * Set description
     *
     * @param string $description
     * @return Reward
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
     * Set amount
     *
     * @param float $amount
     * @return Reward
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set stock
     *
     * @param integer $stock
     * @return Reward
     */
    public function setStock($stock)
    {
        $this->stock = $stock;

        return $this;
    }

    /**
     * Get stock
     *
     * @return integer
     */
    public function getStock()
    {
        return $this->stock;
    }

    /**
     * Set maxQuantityByUser
     *
     * @param integer $maxQuantityByUser
     * @return Reward
     */
    public function setMaxQuantityByUser($maxQuantityByUser)
    {
        $this->maxQuantityByUser = $maxQuantityByUser;

        return $this;
    }

    /**
     * Get maxQuantityByUser
     *
     * @return integer
     */
    public function getMaxQuantityByUser()
    {
        return $this->maxQuantityByUser;
    }

    /**
     * Set project
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\Project $project
     * @return ProjectReward
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
     * Add contributions
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\ProjectContribution $contributions
     * @return ProjectReward
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
}