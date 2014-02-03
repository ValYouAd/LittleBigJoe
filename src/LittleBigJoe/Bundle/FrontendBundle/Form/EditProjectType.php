<?php

namespace LittleBigJoe\Bundle\FrontendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class EditProjectType extends AbstractType
{
		public function __construct($options) 
		{
				$this->options = $options;
		}
	
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {    		
    		$rewards = $options['data']->getRewards();
    		foreach ($rewards as $key => $reward)
    		{
    				if (!empty($this->options) && in_array($reward->getId(), $this->options))
    				{
    						unset($rewards[$key]);
    				}
    		}
    	
    		$ckeditorLanguage = $options['data']->getUser()->getDefaultLanguage();
    		if (empty($ckeditorLanguage))
    		{
    				$ckeditorLanguage = 'en';
    		}
    		// Define default language for CKEditor interface
    		switch ($ckeditorLanguage)
    		{
	    			case 'en': $ckeditorLanguage = 'en-US'; 
	    								 break;
	    			case 'fr': $ckeditorLanguage = 'fr-FR';
	    								 break;
	    			default: 	 $ckeditorLanguage = 'en-US';
	    								 break;
    		}
    		    		
    		$builder
    				->add('description', 'ckeditor', array(
            		'label' => 'Description',
            		'data' => $options['data']->getDescription(),
            		'language' => $ckeditorLanguage
            ))
            ->add('amountRequired', 'text', array(
            		'label' => 'Amount to raise'
            ))
            ->add('rewards', 'collection', array(
            		'type' => new ProjectRewardType(),
            		'allow_add' => true,
            		'allow_delete' => true,
            		'by_reference' => false
            ))
            ->add('submit', 'submit', array(
								'label' => 'Save modifications'
						))
				;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LittleBigJoe\Bundle\CoreBundle\Entity\Project',
        		'validation_groups' => array('Default'),
        		'cascade_validation' => true,
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'editProject';
    }
}
