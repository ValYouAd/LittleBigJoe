<?php

namespace LittleBigJoe\Bundle\FrontendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use FOS\UserBundle\Form\Type\ProfileFormType as BaseType;

class ProfileFormType extends BaseType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->remove('username')
            ->remove('username_canonical')
            ->add('firstname', 'text', array(
                'label' => 'Firstname'
            ))
            ->add('lastname', 'text', array(
                'label' => 'Lastname'
            ))
            ->add('birthday', 'birthday', array(
                'label' => 'Birthday date',
                'years' => range(date('Y') - 100, date('Y'))
            ))
            ->add('facebookUrl', 'url', array(
                'label' => 'Facebook URL',
                'required' => false
            ))
            ->add('twitterUrl', 'url', array(
                'label' => 'Twitter URL',
                'required' => false
            ))
            ->add('googleUrl', 'url', array(
                'label' => 'Google+ URL',
                'required' => false
            ))
            ->add('websiteUrl', 'url', array(
                'label' => 'Website URL',
                'required' => false
            ))
            ->add('city', 'text', array(
                'label' => 'City',
            ))
            ->add('country', 'country', array(
                'label' => 'Country',
            ))
            ->add('nationality', 'text', array(
                'label' => 'Nationality',
            ))
            ->add('defaultLanguage', 'locale', array(
                'label' => 'Default language',
                'choices' => array('en' => 'English', 'fr' => 'French')
            ))
            ->add('photo', 'file', array(
                'label' => 'Photo',
                'attr' => array(
                    'class' => 'file'
                ),
                'data_class' => null,
                'mapped' => true,
                'required' => false
            ))
            ->add('bio', 'textarea', array(
                'label' => 'Bio',
                'required' => false
            ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LittleBigJoe\Bundle\FrontendBundle\Entity\User'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'littlebigjoe_bundle_frontendbundle_user_profile';
    }
}
