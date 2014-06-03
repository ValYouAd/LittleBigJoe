<?php

namespace LittleBigJoe\Bundle\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProjectImageType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'label' => 'backend.name'
            ))
            ->add('path', 'file', array(
                'label' => 'backend.file',
                'data_class' => null,
                'required' => false
            ))
            ->add('highlighted', 'checkbox', array(
                'label' => 'backend.highlighted',
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
            'data_class' => 'LittleBigJoe\Bundle\CoreBundle\Entity\ProjectImage'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'littlebigjoe_bundle_backendbundle_projectimage';
    }
}
