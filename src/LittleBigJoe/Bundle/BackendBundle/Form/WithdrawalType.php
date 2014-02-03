<?php

namespace LittleBigJoe\Bundle\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class WithdrawalType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
		        ->add('beneficiary', 'entity', array(
		        		'label' => 'backend.beneficiary',
		        		'class' => 'LittleBigJoeCoreBundle:Beneficiary',
		        		'query_builder' => function (EntityRepository $er) {
		        			return $er->createQueryBuilder('b')
		        			->orderBy('b.name', 'ASC');
		        		}
		        ))
            ->add('mangopayAmount', 'number', array(
        				'label' => 'backend.withdrawal_amount'
        		))
            ->add('mangopayAmountWithoutFees', 'number', array(
        				'label' => 'backend.withdrawal_amount_without_fees'
        		))
            ->add('mangopayClientFeeAmount', 'number', array(
        				'label' => 'backend.withdrawal_client_fee_amount'
        		))
            ->add('submit', 'submit', array(
            		'label' => 'backend.withdraw',
            		'attr' => array(
            				'class' => 'btn btn-primary'
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
            'data_class' => 'LittleBigJoe\Bundle\CoreBundle\Entity\Withdrawal'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'littlebigjoe_bundle_backendbundle_withdrawal';
    }
}
