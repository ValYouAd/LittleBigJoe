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
                'label' => 'Name'
            ))
            ->add('slug', 'text', array(
                'label' => 'Slug'
            ))
            ->add('brand', 'entity', array(
                'label' => 'Associated brand',
                'class' => 'LittleBigJoeFrontendBundle:Brand',
                'property' => 'name'
            ))
            ->add('category', 'entity', array(
                'label' => 'Associated category',
                'class' => 'LittleBigJoeFrontendBundle:Category',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                    		->where('c.isVisible = :isVisible')
                    		->setParameter('isVisible', true)
                        ->orderBy('c.name', 'ASC');
                }
            ))
            ->add('user', 'entity', array(
                'label' => 'Creator',
                'class' => 'LittleBigJoeFrontendBundle:User',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.lastname', 'ASC');
                }
            ))
            ->add('photo', 'file', array(
                'label' => 'Logo',
                'attr' => array(
                    'class' => 'file'
                ),
                'data_class' => null,
                'mapped' => true,
                'required' => false
            ))
            ->add('location', 'text', array(
                'label' => 'Location'
            ))
            ->add('pitch', 'textarea', array(
                'label' => 'Pitch'
            ))
            ->add('language', 'locale', array(
                'label' => 'Language'
            ))
            ->add('description', 'ckeditor', array(
                'label' => 'Description'
            ))
            ->add('amountRequired', 'number', array(
                'label' => 'Amount to raise'
            ))
            ->remove('amountCount')
            ->add('likesRequired', 'integer', array(
                'label' => 'Likes to get'
            ))
            ->remove('likesCount')
            ->remove('mangopayWalletId')
            ->remove('mangopayCreatedAt')
            ->remove('mangopayUpdatedAt')
            ->add('status', 'choice', array(
                'label' => 'Status',
                'choices' => array(1 => 'Engagement phase', 2 => 'Funding phase')
            ))
            ->add('statusUpdatedAt', 'datetime', array(
                'label' => 'Status updated at'
            ))
            ->add('endingAt', 'datetime', array(
                'label' => 'Project ending at'
            ))
            ->add('deletedAt', 'datetime', array(
                'label' => 'Project deleted at',
                'required' => false
            ))
            ->add('isFavorite', 'choice', array(
                'label' => 'Visibility',
                'choices' => array(0 => 'No', 1 => 'Yes')
            ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LittleBigJoe\Bundle\FrontendBundle\Entity\Project'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'littlebigjoe_bundle_frontendbundle_project';
    }
}
