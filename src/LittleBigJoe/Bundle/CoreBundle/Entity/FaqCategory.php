<?php

namespace LittleBigJoe\Bundle\CoreBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Translatable\Translatable;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * FaqCategory
 *
 * @ORM\Table(name="faq_category")
 * @ORM\Entity(repositoryClass="LittleBigJoe\Bundle\CoreBundle\Repository\FaqCategoryRepository")
 * @Gedmo\TranslationEntity(class="LittleBigJoe\Bundle\CoreBundle\Entity\FaqCategoryTranslation")
 */
class FaqCategory
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
     * @Assert\NotBlank(message = "You must enter your faq category name")
     * @Assert\Length(
     *    min = "2",
     *    max = "250",
     *    minMessage = "Your faq category name must contains at least {{ limit }} characters",
     *    maxMessage = "Your faq category name can't exceed {{ limit }} characters"
     * )
     */
    protected $name;

    /**
     * @ORM\OneToMany(targetEntity="Faq", mappedBy="category", cascade={"persist", "remove"})
     */
    protected $faqs;

    /**
     * @ORM\OneToMany(
     *     targetEntity="FaqCategoryTranslation",
     *  mappedBy="object",
     *  cascade={"persist", "remove"}
     * )
     */
    private $translations;

    public function __construct()
    {
        $this->faqs = new ArrayCollection();
        $this->translations = new ArrayCollection();
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
     * Add faqs
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\Faq $faqs
     * @return Faq
     */
    public function addFaq(\LittleBigJoe\Bundle\CoreBundle\Entity\Faq $faqs)
    {
        $this->faqs[] = $faqs;

        return $this;
    }

    /**
     * Remove faqs
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\Faq $faqs
     */
    public function removeFaq(\LittleBigJoe\Bundle\CoreBundle\Entity\Faq $faqs)
    {
        $this->faqs->removeElement($faqs);
    }

    /**
     * Get faqs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFaqs()
    {
        return $this->faqs;
    }

    /**
     * Add translations
     *
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\FaqCategoryTranslation $translations
     * @return Faq
     */
    public function addTranslation(\LittleBigJoe\Bundle\CoreBundle\Entity\FaqCategoryTranslation $t)
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
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\FaqCategoryTranslation $translations
     */
    public function removeTranslation(\LittleBigJoe\Bundle\CoreBundle\Entity\FaqCategoryTranslation $translations)
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