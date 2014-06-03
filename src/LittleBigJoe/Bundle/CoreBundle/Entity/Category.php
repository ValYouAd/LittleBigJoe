<?php

namespace LittleBigJoe\Bundle\CoreBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Translatable\Translatable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Page
 *
 * @ORM\Table(name="category")
 * @UniqueEntity("slug")
 * @ORM\Entity(repositoryClass="LittleBigJoe\Bundle\CoreBundle\Repository\CategoryRepository")
 * @Gedmo\TranslationEntity(class="LittleBigJoe\Bundle\CoreBundle\Entity\CategoryTranslation")
 */
class Category
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
     * @Gedmo\Translatable
     * @ORM\Column(name="name", type="string", length=255)
     *
     * @Assert\NotBlank(message = "You must enter your category name")
     * @Assert\Length(
     *    min = "2",
     *    max = "250",
     *    minMessage = "Your category name must contains at least {{ limit }} characters",
     *    maxMessage = "Your category name can't exceed {{ limit }} characters"
     * )
     * @Assert\Regex(
     *    pattern = "/^[ÀÁÅÃÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿa-zA-Z -\'\?\&]*$/",
     *    message = "Your category name must only contains letters, spaces, or dashes"
     * )
     */
    protected $name;

    /**
     * @var string
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="slug", type="string", length=255)
     *
     * @Assert\NotBlank(message = "You must enter your category slug")
     * @Assert\Regex(
     *    pattern = "/^[ÀÁÅÃÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿa-zA-Z\-]*$/",
     *    message = "Your category slug must only contains letters, or dashes"
     * )
     */
    protected $slug;

    /**
     * @var string
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="meta_title", type="string", length=255)
     *
     * @Assert\NotBlank(message = "You must enter your META category title")
     * @Assert\Length(
     *    min = "2",
     *    max = "250",
     *    minMessage = "Your META category title must contains at least {{ limit }} characters",
     *    maxMessage = "Your META category title can't exceed {{ limit }} characters"
     * )
     * @Assert\Regex(
     *    pattern = "/^[ÀÁÅÃÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿa-zA-Z -\'\?\&]*$/",
     *    message = "Your META category title must only contains letters, spaces, or dashes"
     * )
     */
    protected $metaTitle;

    /**
     * @var string
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="meta_description", type="string", length=255)
     *
     * @Assert\NotBlank(message = "You must enter your META category description")
     */
    protected $metaDescription;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_visible", type="boolean")
     */
    protected $isVisible;

    /**
     * @ORM\OneToMany(
     *     targetEntity="LittleBigJoe\Bundle\CoreBundle\Entity\CategoryTranslation",
     *  mappedBy="object",
     *  cascade={"persist", "remove"}
     * )
     */
    protected $translations;

    /**
     * @ORM\ManyToMany(targetEntity="Project", mappedBy="categories")
     */
    protected $projects;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
        $this->projects = new ArrayCollection();
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
     * @return Category
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
     * Set metaTitle
     *
     * @param string $metaTitle
     * @return Category
     */
    public function setMetaTitle($metaTitle)
    {
        $this->metaTitle = $metaTitle;

        return $this;
    }

    /**
     * Get metaTitle
     *
     * @return string
     */
    public function getMetaTitle()
    {
        return $this->metaTitle;
    }

    /**
     * Set metaDescription
     *
     * @param string $metaDescription
     * @return Category
     */
    public function setMetaDescription($metaDescription)
    {
        $this->metaDescription = $metaDescription;

        return $this;
    }

    /**
     * Get metaDescription
     *
     * @return string
     */
    public function getMetaDescription()
    {
        return $this->metaDescription;
    }

    /**
     * Set isVisible
     *
     * @param boolean $isVisible
     * @return Category
     */
    public function setIsVisible($isVisible)
    {
        $this->isVisible = $isVisible;

        return $this;
    }

    /**
     * Get isVisible
     *
     * @return boolean
     */
    public function getIsVisible()
    {
        return $this->isVisible;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Category
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
     * Add translations
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\CategoryTranslation $translations
     * @return Category
     */
    public function addTranslation(\LittleBigJoe\Bundle\CoreBundle\Entity\CategoryTranslation $t)
    {
        if (!$this->translations->contains($t)) {
            $this->translations[] = $t;
            $t->setObject($this);
        }

        return $this;
    }

    /**
     * Remove translations
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\CategoryTranslation $translations
     */
    public function removeTranslation(\LittleBigJoe\Bundle\CoreBundle\Entity\CategoryTranslation $translations)
    {
        $this->translations->removeElement($translations);
    }

    /**
     * Get translations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTranslations()
    {
        return $this->translations;
    }

    /**
     * Add projects
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\Project $projects
     * @return Category
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
}