<?php

namespace LittleBigJoe\Bundle\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class ProjectContributionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('mangopayContributionId', 'integer', array(
                'label' => 'Mangopay contribution ID'
            ))
            ->add('mangopayAmount', 'number', array(
                'label' => 'Mangopay amount'
            ))
            ->add('project', 'entity', array(
                'label' => 'Associated project',
                'class' => 'LittleBigJoeFrontendBundle:Project',
                'property' => 'name'
            ))
            ->add('user', 'entity', array(
                'label' => 'Creator',
                'class' => 'LittleBigJoeFrontendBundle:User',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.lastname', 'ASC');
                }
            ))
            ->add('reward', 'entity', array(
                'label' => 'Associated reward',
                'class' => 'LittleBigJoeFrontendBundle:ProjectReward',
                'property' => 'title'
            ))
            ->add('mangopayIsSucceeded', 'choice', array(
                'label' => 'Suceeded payment ?',
                'choices' => array(0 => 'No', 1 => 'Yes')
            ))
            ->add('mangopayIsCompleted', 'choice', array(
                'label' => 'Completed payment ?',
                'choices' => array(0 => 'No', 1 => 'Yes')
            ))
            ->add('mangopayError', 'text', array(
                'label' => 'Mangopay error',
                'required' => false
            ))
            ->add('mangopayAnswerCode', 'text', array(
                'label' => 'Mangopay answer code',
                'required' => false
            ))
            ->add('mangopayCreatedAt', 'datetime', array(
                'label' => 'Mangopay payment created at'
            ))
            ->add('mangopayUpdatedAt', 'datetime', array(
                'label' => 'Mangopay payment updated at'
            ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LittleBigJoe\Bundle\FrontendBundle\Entity\ProjectContribution'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'littlebigjoe_bundle_frontendbundle_projectcontribution';
    }
}
