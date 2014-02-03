<?php

namespace LittleBigJoe\Bundle\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class BeneficiaryType extends AbstractType
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
            ->add('bankAccountOwnerName', 'text', array(
                'label' => 'backend.bank_account_owner_name'
            ))
            ->add('bankAccountOwnerAddress', 'text', array(
                'label' => 'backend.bank_account_owner_address'
            ))
            ->add('bankAccountIban', 'text', array(
                'label' => 'backend.bank_account_iban'
            ))
            ->add('bankAccountBic', 'text', array(
                'label' => 'backend.bank_account_bic'
            ))
            ->add('user', 'entity', array(
                'label' => 'backend.creator',
            		'required' => false,
                'class' => 'LittleBigJoeCoreBundle:User',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.lastname', 'ASC');
                }
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LittleBigJoe\Bundle\CoreBundle\Entity\Beneficiary'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'littlebigjoe_bundle_backendbundle_beneficiary';
    }
}
