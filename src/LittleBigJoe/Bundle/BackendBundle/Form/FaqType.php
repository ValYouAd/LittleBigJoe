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
                'translatable_class' => "LittleBigJoe\Bundle\CoreBundle\Entity\Faq",
                'fields' => array(
                    'question' => array(
                        'type' => 'ckeditor',
                        'label' => 'backend.question',
                    		'required' => false
                    ),
                    'answer' => array(
                        'type' => 'ckeditor',
                        'label' => 'backend.answer',
                    		'required' => false
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
            'data_class' => 'LittleBigJoe\Bundle\CoreBundle\Entity\Faq'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'littlebigjoe_bundle_backendbundle_faq';
    }
}
