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
                'translatable_class' => "LittleBigJoe\Bundle\FrontendBundle\Entity\Category",
                'fields' => array(
                    'name' => array(
                        'type' => 'text',
                        'label' => 'Category name'
                    ),
                    'slug' => array(
                        'type' => 'text',
                        'label' => 'Slug'
                    ),
                    'metaTitle' => array(
                        'type' => 'text',
                        'label' => 'META Title'
                    ),
                    'metaDescription' => array(
                        'type' => 'textarea',
                        'label' => 'META Description'
                    )
                )
            ))
            ->add('isVisible', 'choice', array(
                'label' => 'Visibility',
                'choices' => array(0 => 'Not visible', 1 => 'Visible')
            ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LittleBigJoe\Bundle\FrontendBundle\Entity\Category'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'littlebigjoe_bundle_frontendbundle_category';
    }
}
