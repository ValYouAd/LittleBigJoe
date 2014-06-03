<?php

namespace LittleBigJoe\Bundle\FrontendBundle\Form;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use FOS\UserBundle\Form\Type\ProfileFormType as BaseType;

class ProfileFormType extends BaseType
{
    private $container;

    public function __construct($class, ContainerInterface $container)
    {
        parent::__construct($class);
        $this->container = $container;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $lang = $this->container->get('request')->getLocale();
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
                'widget' => 'single_text',
                'format' => $format,
                'attr' => array('class' => 'form-control datepicker'),
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
            'data_class' => 'LittleBigJoe\Bundle\CoreBundle\Entity\User'
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
