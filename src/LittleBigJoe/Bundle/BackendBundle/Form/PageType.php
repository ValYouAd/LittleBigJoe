<?php

namespace LittleBigJoe\Bundle\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PageType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('translations', 'a2lix_translations_gedmo', array(
                'translatable_class' => "LittleBigJoe\Bundle\CoreBundle\Entity\Page",
                'fields' => array(
                    'title' => array(
                        'field_type' => 'text',
                        'label' => 'backend.title'
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
                    ),
                    'content' => array(
                        'field_type' => 'ckeditor',
                        'label' => 'backend.content',
                    		'required' => false,
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
            'data_class' => 'LittleBigJoe\Bundle\CoreBundle\Entity\Page'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'littlebigjoe_bundle_backendbundle_page';
    }
}
