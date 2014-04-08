<?php

namespace LittleBigJoe\Bundle\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class ProjectType extends AbstractType
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
            ->add('brand', 'entity', array(
                'label' => 'backend.associated_brand',
                'class' => 'LittleBigJoeCoreBundle:Brand',
                'property' => 'name'
            ))
            ->add('productType', 'entity', array(
                'label' => 'backend.product_type',
                'class' => 'LittleBigJoeCoreBundle:ProductType',
                'property' => 'name'
            ))
            ->add('categories', 'entity', array(
                'label' => 'backend.associated_categories',
                'class' => 'LittleBigJoeCoreBundle:Category',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                    		->where('c.isVisible = :isVisible')
                    		->setParameter('isVisible', true)
                        ->orderBy('c.name', 'ASC');
                },
                'multiple' => true,
                'expanded' => true
            ))
            ->add('user', 'entity', array(
                'label' => 'backend.creator',
                'class' => 'LittleBigJoeCoreBundle:User',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.lastname', 'ASC');
                }
            ))
            ->add('photo', 'file', array(
                'label' => 'backend.logo',
                'attr' => array(
                    'class' => 'file'
                ),
                'data_class' => null,
                'mapped' => true,
                'required' => false
            ))
            ->add('location', 'text', array(
                'label' => 'backend.location'
            ))
            ->add('pitch', 'textarea', array(
                'label' => 'backend.pitch'
            ))
            ->add('language', 'locale', array(
                'label' => 'backend.language'
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
            ->add('amountRequired', 'number', array(
                'label' => 'backend.amount_to_raise'
            ))
            ->remove('amountCount')
            ->add('likesRequired', 'integer', array(
                'label' => 'backend.likes_to_get'
            ))
            ->remove('likesCount')
            ->remove('mangopayWalletId')
            ->remove('mangopayCreatedAt')
            ->remove('mangopayUpdatedAt')
            ->add('status', 'choice', array(
                'label' => 'backend.status',
                'choices' => array(1 => 'backend.engagement_phase', 2 => 'backend.funding_phase')
            ))
            ->add('statusUpdatedAt', 'datetime', array(
                'label' => 'backend.status_updated_at'
            ))
            ->add('endingAt', 'datetime', array(
                'label' => 'backend.project_ending_at'
            ))
            ->add('endedAt', 'datetime', array(
            		'label' => 'backend.project_ended_at',
                'required' => false
            ))
            ->add('deletedAt', 'datetime', array(
                'label' => 'backend.project_deleted_at',
                'required' => false
            ))
            ->add('isFavorite', 'choice', array(
            		'label' => 'backend.is_favorite',
            		'choices' => array(0 => 'backend.no', 1 => 'backend.yes')
            ))
            ->add('hasBrandRepresentation', 'choice', array(
            		'label' => 'backend.has_brand_representation',
            		'choices' => array(0 => 'backend.no', 1 => 'backend.yes')
            ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LittleBigJoe\Bundle\CoreBundle\Entity\Project'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'littlebigjoe_bundle_backendbundle_project';
    }
}
