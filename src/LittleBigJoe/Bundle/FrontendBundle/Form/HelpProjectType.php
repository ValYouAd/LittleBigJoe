<?php

namespace LittleBigJoe\Bundle\FrontendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class HelpProjectType extends AbstractType
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
        $builder
            ->add('price', 'number', array(
                'label' => 'Price',
            ))
            ->add('currency', 'currency', array(
                'label' => 'Currency',    
                'choices' => array('USD' => 'USD', 'EUR' => 'EUR', 'GBP' => 'GBP', 'JPY' => 'JPY')            
            ))
            ->add('quantity', 'integer', array(
                'label' => 'Quantity',
            ))
            ->add('reason', 'textarea', array(
            	'label' => 'Reason',
            ));
            
         if ($this->options['loggedFb'])
         {  
            $builder->add('sharedFacebook', 'checkbox', array(
                'label' => 'Share my support on Facebook',
                'attr' => array('checked' => 'checked'),
            ));
         }
         if ($this->options['loggedTwitter'])
         {
            $builder->add('sharedTwitter', 'checkbox', array(
                'label' => 'Share my support on Twitter',
                'attr' => array('checked' => 'checked'),
            ));
         }
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LittleBigJoe\Bundle\CoreBundle\Entity\ProjectHelp'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'helpproject';
    }
}
