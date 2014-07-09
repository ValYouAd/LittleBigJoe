<?php

namespace LittleBigJoe\Bundle\BackEndBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Translation\Translator;

class CodeType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $translator = new Translator('fr_FR');
        $builder
            ->add('code', 'text', array(
                'label' => $translator->trans('backend.code')
            ))
            ->add('used', 'integer', array(
                'label' => $translator->trans('backend.used')
            ))
            ->add('max_use', 'integer', array(
                'label' => $translator->trans('backend.max_use')
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LittleBigJoe\Bundle\CoreBundle\Entity\Code'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'littlebigjoe_bundle_corebundle_code';
    }
}
