<?php

namespace LittleBigJoe\Bundle\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FaqType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('translations', 'a2lix_translations_gedmo', array(
                'translatable_class' => "LittleBigJoe\Bundle\FrontendBundle\Entity\Faq",
                'fields' => array(
                    'question' => array(
                        'type' => 'ckeditor',
                        'label' => 'Question'
                    ),
                    'answer' => array(
                        'type' => 'ckeditor',
                        'label' => 'Answer'
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
            'data_class' => 'LittleBigJoe\Bundle\FrontendBundle\Entity\Faq'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'littlebigjoe_bundle_frontendbundle_faq';
    }
}
