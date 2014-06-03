<?php

namespace LittleBigJoe\Bundle\FrontendBundle\Form;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;
use Symfony\Component\Validator\Constraints\Date;

class RegistrationFormType extends BaseType
{
		private $session;
        private $container;
		
		public function __construct($session, $class, ContainerInterface $container)
		{
            $this->session = $session;
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
        
        // Get session vars (setted via OAuth) if they are setted
        $firstname = $this->session->get('oauth_firstname', '');
        $lastname = $this->session->get('oauth_lastname', '');
        $email = $this->session->get('oauth_email', '');
        $location = $this->session->get('oauth_location', '');
        $lang = $this->session->get('oauth_lang', $this->container->get('request')->getLocale());
        $bio = $this->session->get('oauth_bio', '');
        $twitterUrl = $this->session->get('oauth_twitterUrl', '');
        $websiteUrl = $this->session->get('oauth_websiteUrl', '');
        
        // Set default lang based on default browser language, if it's not retrieved via OAuth
        if (empty($lang))
        {
        		$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        }
        // Check if default lang is authorized
        $langs = array('en' => 'English', 'fr' => 'French');        
        if (!array_key_exists($lang, $langs))
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
            ->add('email', 'email', array(
        				'label' => 'Email address',
            		'data' => $email
        		))
            ->add('firstname', 'text', array(
                'label' => 'Firstname',
            		'data' => $firstname
            ))
            ->add('lastname', 'text', array(
                'label' => 'Lastname',
            		'data' => $lastname
            ))
            ->add('birthday', 'date', array(
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
            		'data' => $twitterUrl,
                'required' => false
            ))
            ->add('googleUrl', 'url', array(
                'label' => 'Google+ URL',
                'required' => false
            ))
            ->add('websiteUrl', 'url', array(
                'label' => 'Website URL',
            		'data' => $websiteUrl,
                'required' => false
            ))
            ->add('city', 'text', array(
                'label' => 'City',
            		'data' => $location
            ))
            ->add('country', 'country', array(
                'label' => 'Country',
            ))
            ->add('defaultLanguage', 'locale', array(
                'label' => 'Default language',
                'choices' => $langs,
            		'preferred_choices' => array($lang)
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
            		'data' => $bio,
                'required' => false
            ))
            ->remove('ipAddress');
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
        return 'littlebigjoe_bundle_frontendbundle_user_registration';
    }
}
