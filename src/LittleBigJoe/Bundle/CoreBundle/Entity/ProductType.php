<?php

namespace LittleBigJoe\Bundle\CoreBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Translatable\Translatable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ProductType
 *
 * @ORM\Table(name="product_type")
 * @UniqueEntity("name")
 * @ORM\Entity(repositoryClass="LittleBigJoe\Bundle\CoreBundle\Repository\ProductTypeRepository")
 */
class ProductType
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
     * @Assert\NotBlank(message = "You must enter your product type name")
     * @Assert\Length(
     *    min = "2",
     *    max = "250",
     *    minMessage = "Your product type name must contains at least {{ limit }} characters",
     *    maxMessage = "Your product type name can't exceed {{ limit }} characters"
     * )
     * @Assert\Regex(
     *    pattern = "/^[ÀÁÅÃÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿa-zA-Z -\'\?\&]*$/",
     *    message = "Your product type name must only contains letters, spaces, or dashes"
     * )
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="language", type="string", length=45)
     */
    protected $language;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_visible", type="boolean")
     */
    protected $isVisible;

    /**
     * @ORM\OneToMany(targetEntity="Project", mappedBy="productType", cascade={"persist", "remove"})
     */
    protected $projects;

    public function __toString()
    {
        return $this->name;
    }

    public function __construct()
    {
        $this->projects = new ArrayCollection();
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
     * @return ProductType
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
     * Set isVisible
     *
     * @param boolean $isVisible
     * @return ProductType
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
     * Add projects
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\Project $projects
     * @return ProductType
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
     * Set language
     *
     * @param string $language
     * @return ProductType
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
}