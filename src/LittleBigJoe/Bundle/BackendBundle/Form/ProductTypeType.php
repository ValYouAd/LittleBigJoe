<?php

namespace LittleBigJoe\Bundle\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProductTypeType extends AbstractType
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
            ->add('language', 'locale', array(
                'label' => 'backend.language'
            ))
            ->add('isVisible', 'choice', array(
                'label' => 'backend.visibility',
                'choices' => array(0 => 'backend.invisible', 1 => 'backend.visible')
            ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LittleBigJoe\Bundle\CoreBundle\Entity\ProductType'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'littlebigjoe_bundle_backendbundle_product_type';
    }
}
