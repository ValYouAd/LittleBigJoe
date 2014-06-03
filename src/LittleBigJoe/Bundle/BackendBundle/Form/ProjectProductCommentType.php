<?php

namespace LittleBigJoe\Bundle\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProjectProductCommentType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content', 'textarea', array(
                'label' => 'backend.content'
            ))
            ->add('product', 'entity', array(
                'label' => 'backend.product',
                'class' => 'LittleBigJoeCoreBundle:ProjectProduct',
                'property' => 'name'
            ))
            ->add('user', 'entity', array(
                'label' => 'backend.user',
                'class' => 'LittleBigJoeCoreBundle:User',
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LittleBigJoe\Bundle\CoreBundle\Entity\ProjectProductComment'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'littlebigjoe_bundle_backendbundle_projectproductcomment';
    }
}
