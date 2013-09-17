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
                'label' => 'backend.mangopay_contribution_id'
            ))
            ->add('mangopayAmount', 'number', array(
                'label' => 'backend.mangopay_amount'
            ))
            ->add('project', 'entity', array(
                'label' => 'backend.associated_project',
                'class' => 'LittleBigJoeCoreBundle:Project',
                'property' => 'name'
            ))
            ->add('user', 'entity', array(
                'label' => 'backend.creator',
                'class' => 'LittleBigJoeCoreBundle:User',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.lastname', 'ASC');
                }
            ))
            ->add('reward', 'entity', array(
                'label' => 'backend.associated_reward',
                'class' => 'LittleBigJoeCoreBundle:ProjectReward',
                'property' => 'title'
            ))
            ->add('mangopayIsSucceeded', 'choice', array(
                'label' => 'backend.succeeded_payment',
                'choices' => array(0 => 'backend.no', 1 => 'backend.yes')
            ))
            ->add('mangopayIsCompleted', 'choice', array(
                'label' => 'backend.completed_payment',
                'choices' => array(0 => 'backend.no', 1 => 'backend.yes')
            ))
            ->add('mangopayError', 'text', array(
                'label' => 'backend.mangopay_error',
                'required' => false
            ))
            ->add('mangopayAnswerCode', 'text', array(
                'label' => 'backend.mangopay_answer_code',
                'required' => false
            ))
            ->add('mangopayCreatedAt', 'datetime', array(
                'label' => 'backend.mangopay_payment_created_at'
            ))
            ->add('mangopayUpdatedAt', 'datetime', array(
                'label' => 'backend.mangopay_payment_updated_at'
            ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LittleBigJoe\Bundle\CoreBundle\Entity\ProjectContribution'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'littlebigjoe_bundle_backendbundle_projectcontribution';
    }
}
