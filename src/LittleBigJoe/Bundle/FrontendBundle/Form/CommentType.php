<?php

namespace LittleBigJoe\Bundle\FrontendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CommentType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        		->add('project', 'hidden', array(
        				'data' => $options['data']->getProject()->getId()
        		))
            ->add('content', 'ckeditor', array(
			      		'label' => 'Comment content',
			      		'data' => '',
			      		'toolbar' => array('clipboard', 'basicstyles', 'links', 'insert', 'tools'),
			      		'toolbar_groups' => array(
			      				'document' => array(),
			      				'clipboard' => array('Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo'),
			      				'editing' => array(),
			      				'basicstyles' => array('Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat'),
			      				'paragraph' => array('NumberedList','BulletedList','-','Outdent','Indent','-','JustifyLeft', 'JustifyCenter','JustifyRight','JustifyBlock'),
			      				'links' => array('Link','oembed', 'Unlink','Anchor'),
			      				'insert' => array('Table'),
			      				'styles' => array(),
			      				'tools' => array('Maximize')
			      		)
			      ))
			      ->add('addComment', 'button', array(
			      		'label' => 'Comment this project',
			      		'attr' => array(
			      				'class' => 'btn btn-success'
			      		)
			      ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LittleBigJoe\Bundle\CoreBundle\Entity\Comment'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'comment';
    }
}
