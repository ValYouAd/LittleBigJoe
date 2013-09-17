<?php

namespace LittleBigJoe\Bundle\CoreBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Translatable\Translatable;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Page
 *
 * @ORM\Table(name="page")
 * @UniqueEntity("slug")
 * @ORM\Entity(repositoryClass="LittleBigJoe\Bundle\CoreBundle\Repository\PageRepository")
 * @Gedmo\TranslationEntity(class="LittleBigJoe\Bundle\CoreBundle\Entity\PageTranslation")
 */
class Page
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
     * @Gedmo\Translatable
     * @ORM\Column(name="title", type="string", length=255)
     *
     * @Assert\NotBlank(message = "You must enter your page title")
     * @Assert\Length(
     *    min = "2",
     *    max = "250",
     *    minMessage = "Your page title must contains at least {{ limit }} characters",
     *    maxMessage = "Your page title can't exceed {{ limit }} characters"
     * )
     * @Assert\Regex(
     *    pattern = "/^[ÀÁÅÃÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿa-zA-Z -]*$/",
     *    message = "Your page title must only contains letters, spaces, or dashes"
     * )
     */
    private $title;

    /**
     * @var string
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="meta_title", type="string", length=255)
     *
     * @Assert\NotBlank(message = "You must enter your META page title")
     * @Assert\Length(
     *    min = "2",
     *    max = "250",
     *    minMessage = "Your META page title must contains at least {{ limit }} characters",
     *    maxMessage = "Your META page title can't exceed {{ limit }} characters"
     * )
     * @Assert\Regex(
     *    pattern = "/^[ÀÁÅÃÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿa-zA-Z -]*$/",
     *    message = "Your META page title must only contains letters, spaces, or dashes"
     * )
     */
    private $metaTitle;

    /**
     * @var string
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="meta_description", type="string", length=255)
     *
     * @Assert\NotBlank(message = "You must enter your META page description")
     */
    private $metaDescription;

    /**
     * @var string
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="slug", type="string", length=255)
     *
     * @Assert\NotBlank(message = "You must enter your page slug")
     * @Assert\Regex(
     *    pattern = "/^[ÀÁÅÃÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿa-zA-Z\-]*$/",
     *    message = "Your page slug must only contains letters, or dashes"
     * )
     */
    private $slug;

    /**
     * @var string
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="content", type="text")
     *
     * @Assert\NotBlank(message = "You must enter your page content")
     */
    private $content;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_visible", type="boolean")
     */
    private $isVisible;

    /**
     * @ORM\OneToMany(
     *     targetEntity="PageTranslation",
     *  mappedBy="object",
     *  cascade={"persist", "remove"}
     * )
     */
    private $translations;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
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
     * @return Page
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
     * Set metaTitle
     *
     * @param string $metaTitle
     * @return Page
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
     * @return Page
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
     * Set slug
     *
     * @param string $slug
     * @return Page
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
     * Set content
     *
     * @param string $content
     * @return Page
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
     * Set isVisible
     *
     * @param boolean $isVisible
     * @return Page
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
     * Add translations
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\PageTranslation $translations
     * @return Page
     */
    public function addTranslation(\LittleBigJoe\Bundle\CoreBundle\Entity\PageTranslation $t)
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
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\PageTranslation $translations
     */
    public function removeTranslation(\LittleBigJoe\Bundle\CoreBundle\Entity\PageTranslation $translations)
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
}