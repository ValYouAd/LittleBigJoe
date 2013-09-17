<?php

namespace LittleBigJoe\Bundle\CoreBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Translatable\Translatable;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Faq
 *
 * @ORM\Table(name="faq")
 * @ORM\Entity(repositoryClass="LittleBigJoe\Bundle\CoreBundle\Repository\FaqRepository")
 * @Gedmo\TranslationEntity(class="LittleBigJoe\Bundle\CoreBundle\Entity\FaqTranslation")
 */
class Faq
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
     * @ORM\Column(name="question", type="string", length=255)
     *
     * @Assert\NotBlank(message = "You must enter your question")
     */
    private $question;

    /**
     * @var string
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="answer", type="string", length=255)
     *
     * @Assert\NotBlank(message = "You must enter your answer")
     */
    private $answer;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_visible", type="boolean")
     */
    private $isVisible;

    /**
     * @ORM\OneToMany(
     *     targetEntity="FaqTranslation",
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
        return $this->answer;
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
     * Set question
     *
     * @param string $question
     * @return Faq
     */
    public function setQuestion($question)
    {
        $this->question = $question;

        return $this;
    }

    /**
     * Get question
     *
     * @return string
     */
    public function getQuestion()
    {
        return html_entity_decode($this->question);
    }

    /**
     * Set answer
     *
     * @param string $answer
     * @return Faq
     */
    public function setAnswer($answer)
    {
        $this->answer = $answer;

        return $this;
    }

    /**
     * Get answer
     *
     * @return string
     */
    public function getAnswer()
    {
        return html_entity_decode($this->answer);
    }

    /**
     * Set isVisible
     *
     * @param boolean $isVisible
     * @return Faq
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
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\FaqTranslation $translations
     * @return Faq
     */
    public function addTranslation(\LittleBigJoe\Bundle\CoreBundle\Entity\FaqTranslation $t)
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
     * @param \LittleBigJoe\Bundle\CoreBundle\Entity\FaqTranslation $translations
     */
    public function removeTranslation(\LittleBigJoe\Bundle\CoreBundle\Entity\FaqTranslation $translations)
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