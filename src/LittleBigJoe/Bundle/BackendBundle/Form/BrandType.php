<?php

namespace LittleBigJoe\Bundle\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BrandType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'label' => 'backend.name'
            ))
            ->add('slug', 'text', array(
                'label' => 'backend.slug'
            ))
            ->add('logo', 'file', array(
                'label' => 'backend.logo',
                'attr' => array(
                    'class' => 'file'
                ),
                'data_class' => null,
                'mapped' => true,
                'required' => false
            ))
            ->add('description', 'ckeditor', array(
                'label' => 'backend.description',
            		'toolbar' => array('document', 'clipboard', 'paragraph', '/', 'basicstyles', 'links', 'insert', 'styles', 'tools'),
                'toolbar_groups' => array(
                    'document' => array('Source'),
		            		'clipboard' => array('Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo'),
		            		'editing' => array(),
		            		'basicstyles' => array('Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat'),
		            		'paragraph' => array('NumberedList','BulletedList','-','Outdent','Indent','-','JustifyLeft', 'JustifyCenter','JustifyRight','JustifyBlock'),
		            		'links' => array('Link','oembed', 'Unlink','Anchor'),
		            		'insert' => array('Image','Table'),
		            		'styles' => array('Styles','Format'),
		            		'tools' => array('Maximize')
                )
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
            ->add('contactName', 'text', array(
                'label' => 'backend.contact_name',
            		'required' => false
            ))
            ->add('contactStatus', 'text', array(
                'label' => 'backend.contact_status',
            		'required' => false
            ))
            ->add('contactPhone', 'number', array(
                'label' => 'backend.contact_phone_number',
            		'required' => false
            ))
            ->add('contactEmail', 'email', array(
                'label' => 'backend.contact_email',
            		'required' => false
            ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LittleBigJoe\Bundle\CoreBundle\Entity\Brand'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'littlebigjoe_bundle_backendbundle_brand';
    }
}
