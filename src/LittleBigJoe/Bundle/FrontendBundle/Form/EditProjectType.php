<?php

namespace LittleBigJoe\Bundle\FrontendBundle\Form;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class EditProjectType extends AbstractType
{
    private $options;

    public function __construct($options = array())
    {
            $this->options = $options;
    }
	
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $lang = $this->options['locale'];
        if (empty($lang))
        {
            $lang = 'en';
        }
        switch ($lang)
        {
            case 'en': $format = 'MM/dd/yyyy'; break;
            case 'fr': $format = 'dd/MM/yyyy'; break;
            default: $format = 'MM/dd/yyyy'; break;
        }

        $builder
            ->add('endingAt', 'date', array(
                'label' => 'Ending at',
                'widget' => 'single_text',
                'format' => $format,
                'attr' => array('class' => 'form-control datepicker'),
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
        		'validation_groups' => array('editProject'),
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
