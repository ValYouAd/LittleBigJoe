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
                        'field_type' => 'ckeditor',
                        'label' => 'backend.question',
                    		'required' => false,
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
				            ),
                    'answer' => array(
                        'field_type' => 'ckeditor',
                        'label' => 'backend.answer',
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
