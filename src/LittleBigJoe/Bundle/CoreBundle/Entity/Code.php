<?php

namespace LittleBigJoe\Bundle\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Code
 *
 * @ORM\Table(name="codes")
 * @ORM\Entity(repositoryClass="LittleBigJoe\Bundle\CoreBundle\Entity\CodeRepository")
 *
 * @UniqueEntity(fields="code", message="Ce code est invalide.")
 */
class Code
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
     * @ORM\Column(name="code", type="string", unique=true, length=255)
     */
    private $code;

    /**
     * @var integer
     *
     * @ORM\Column(name="used", type="integer")
     */
    private $used = 0;

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
     * Set code
     *
     * @param string $code
     * @return Code
     */
    public function setCode($code)
    {
        $this->code = $code;
    
        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set used
     *
     * @param integer $used
     * @return Code
     */
    public function setUsed($used)
    {
        $this->used = $used;
    
        return $this;
    }

    /**
     * Get used
     *
     * @return integer 
     */
    public function getUsed()
    {
        return $this->used;
    }
}
