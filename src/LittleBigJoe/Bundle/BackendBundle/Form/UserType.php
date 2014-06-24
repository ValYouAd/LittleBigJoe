<?php

namespace LittleBigJoe\Bundle\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;
use LittleBigJoe\Bundle\CoreBundle\Entity\User;

class UserType extends BaseType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $user = new User();

        $builder
            ->remove('plainPassword')
            ->add('plainPassword', 'repeated', array(
                'options' => array('required' => true),
                'first_options'  => array('label' => 'backend.password'),
                'second_options' => array('label' => 'backend.password_verification'),
                'required' => false
            ))
            ->add('firstname', 'text', array(
                'label' => 'backend.firstname'
            ))
            ->add('lastname', 'text', array(
                'label' => 'backend.lastname'
            ))
            ->add('birthday', 'birthday', array(
                'label' => 'backend.birthday_date',
                'years' => range(date('Y') - 100, date('Y'))
            ))
            ->add('facebookUrl', 'url', array(
                'label' => 'backend.facebook_url',
                'required' => false
            ))
            ->add('twitterUrl', 'url', array(
                'label' => 'backend.twitter_url',
                'required' => false
            ))
            ->add('googleUrl', 'url', array(
                'label' => 'backend.googleplus_url',
                'required' => false
            ))
            ->add('websiteUrl', 'url', array(
                'label' => 'backend.website_url',
                'required' => false
            ))
            ->add('city', 'text', array(
                'label' => 'backend.city',
            ))
            ->add('country', 'country', array(
                'label' => 'backend.country',
            ))
            ->add('defaultLanguage', 'locale', array(
                'label' => 'backend.default_language',
                'required' => false
            ))
            ->add('photo', 'file', array(
                'label' => 'backend.photo',
                'attr' => array(
                    'class' => 'file'
                ),
                'data_class' => null,
                'mapped' => true,
                'required' => false
            ))
            ->add('bio', 'textarea', array(
                'label' => 'backend.bio',
                'required' => false
            ))
            ->add('ipAddress', 'text', array(
                'label' => 'backend.ip_address',
                'data' => $_SERVER['REMOTE_ADDR']
            ))
            ->add('roles', 'choice', array(
                'label' => 'backend.roles',
                'choices' => array('ROLE_USER' => 'backend.user', 'ROLE_BRAND_ADMIN' => 'backend.brand_admin', 'ROLE_SUPER_ADMIN' => 'backend.lbj_staff'),
                'multiple' => true,
                'expanded' => false,
            ))
            ->add('brands', 'entity', array(
                'label' => 'backend.brands',
                'class' => 'LittleBigJoeCoreBundle:Brand',
                'property' => 'name',
                'multiple' => true,
                'expanded' => false,
                'required' => false
            ))
            ->remove('username')
            ->remove('usernameCanonical')
            ->remove('personType')
            ->remove('mangopayUserId')
            ->remove('mangopayCreatedAt')
            ->remove('mangopayUpdatedAt');
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
        return 'littlebigjoe_bundle_backendbundle_user';
    }
}
