<?php

namespace LittleBigJoe\Bundle\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CategoryType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('translations', 'a2lix_translations_gedmo', array(
                'translatable_class' => "LittleBigJoe\Bundle\CoreBundle\Entity\Category",
                'fields' => array(
                    'name' => array(
                        'field_type' => 'text',
                        'label' => 'backend.name'
                    ),
                    'slug' => array(
                        'field_type' => 'text',
                        'label' => 'backend.slug'
                    ),
                    'metaTitle' => array(
                        'field_type' => 'text',
                        'label' => 'backend.meta_title'
                    ),
                    'metaDescription' => array(
                        'field_type' => 'textarea',
                        'label' => 'backend.meta_description'
                    )
                )
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
            'data_class' => 'LittleBigJoe\Bundle\CoreBundle\Entity\Category'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'littlebigjoe_bundle_backendbundle_category';
    }
}
