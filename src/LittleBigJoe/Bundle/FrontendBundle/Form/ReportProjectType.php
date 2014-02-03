<?php

namespace LittleBigJoe\Bundle\FrontendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use LittleBigJoe\Bundle\CoreBundle\Entity\User;
use LittleBigJoe\Bundle\CoreBundle\Entity\Project;

class ReportProjectType extends AbstractType
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
            ->add('subject', 'choice', array(
            	'label' => 'Report reason',
                'choices' => $this->options['reportReasons']
            ))
            ->add('reasonDetails', 'textarea', array(
            	'label' => 'Details',
                'required' => false
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => null
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'projectreport';
    }
}
