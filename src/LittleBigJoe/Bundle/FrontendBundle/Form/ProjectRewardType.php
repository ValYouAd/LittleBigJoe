<?php

namespace LittleBigJoe\Bundle\FrontendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProjectRewardType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array(
        				'label' => 'Reward title',
            		'required' => false
        		))
            ->add('description', 'textarea', array(
        				'label' => 'Reward description',
            		'required' => false
        		))
            ->add('amount', 'integer', array(
        				'label' => 'Required amount',
            		'required' => false
        		))
            ->add('stock', 'integer', array(
        				'label' => 'Stock',
            		'required' => false
        		))
            ->add('maxQuantityByUser', 'integer', array(
        				'label' => 'Maximum quantity by user',
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
            'data_class' => 'LittleBigJoe\Bundle\CoreBundle\Entity\ProjectReward'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'littlebigjoe_bundle_frontendbundle_projectreward';
    }
}
