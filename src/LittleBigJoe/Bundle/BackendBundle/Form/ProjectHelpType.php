<?php

namespace LittleBigJoe\Bundle\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class ProjectHelpType extends AbstractType
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
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                    ->orderBy('p.name', 'ASC');
                }
            ))
            ->add('user', 'entity', array(
                'label' => 'backend.submitter',
                'class' => 'LittleBigJoeCoreBundle:User',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                    ->orderBy('u.lastname', 'ASC');
                }
            ))
            ->add('price', 'number', array(
                'label' => 'backend.price',
                'required' => false
            ))
            ->add('currency', 'currency', array(
                'label' => 'backend.currency',    
                'choices' => array('USD' => 'USD', 'EUR' => 'EUR', 'GBP' => 'GBP', 'JPY' => 'JPY'),
                'required' => false            
            ))
            ->add('quantity', 'integer', array(
                'label' => 'backend.quantity',
                'required' => false
            ))
            ->add('reason', 'textarea', array(
            	'label' => 'backend.reason',
                'required' => false
            ))
            ->add('sharedFacebook', 'checkbox', array(
            	'label' => 'backend.shared_on_facebook',
                'required' => false                
            ))
            ->add('sharedTwitter', 'checkbox', array(
            	'label' => 'backend.shared_on_twitter'  ,
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
            'data_class' => 'LittleBigJoe\Bundle\CoreBundle\Entity\ProjectHelp'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'littlebigjoe_bundle_backendbundle_projecthelp';
    }
}
