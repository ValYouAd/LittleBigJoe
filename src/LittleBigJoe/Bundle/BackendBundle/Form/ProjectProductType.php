<?php

namespace LittleBigJoe\Bundle\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProjectProductType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('project', 'entity', array(
                'label' => 'backend.project',
                'class' => 'LittleBigJoeCoreBundle:Project',
                'property' => 'name'
            ))
            ->add('name', 'text', array(
                'label' => 'backend.name'
            ))
            ->add('pitch', 'textarea', array(
                'label' => 'backend.pitch'
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
            ->add('endingAt', 'date', array(
                'label' => 'backend.ending_at'
            ))
            ->add('giftProduct', 'choice', array(
                'label' => 'backend.project_owner_gift',
                'choices' => array('1' => 'backend.offer_product_to_owner', '0' => 'backend.offer_percentage_raised_funds')
            ))
            ->add('giftPercentageFundsRaised', 'integer', array(
                'label' => '',
                'required' => false
            ))
            ->add('submittedAt', 'datetime', array(
                'label' => 'backend.submitted_at'
            ))
            ->add('validatedAt', 'datetime', array(
                'label' => 'backend.validated_at',
                'required' => false
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LittleBigJoe\Bundle\CoreBundle\Entity\ProjectProduct'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'littlebigjoe_bundle_backendbundle_projectproduct';
    }
}
