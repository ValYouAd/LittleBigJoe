<?php

namespace LittleBigJoe\Bundle\FrontendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class CreateProductFormType extends AbstractType
{
    private $options;

    public function __construct($options = array())
    {
        $this->options = $options;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $ckeditorLanguage = $options['data']->getProject()['user']['defaultLanguage'];
        if (empty($ckeditorLanguage))
        {
            $ckeditorLanguage = 'en';
        }
        // Define default language for CKEditor interface
        switch ($ckeditorLanguage)
        {
            case 'en': $ckeditorLanguage = 'en-US'; $format = 'MM/dd/yyyy';
                break;
            case 'fr': $ckeditorLanguage = 'fr-FR'; $format = 'dd/MM/yyyy';
                break;
            default: 	 $ckeditorLanguage = 'en-US'; $format = 'MM/dd/yyyy';
            break;
        }

        switch ($options['flow_step'])
        {
            // Step 1 : Create my project
            case 1: $builder
                ->add('name', 'text', array(
                    'label' => 'Name'
                ))
                ->add('pitch', 'textarea', array(
                    'label' => 'Pitch'
                ));
                break;

            // Step 2 : Present my project
            case 2: $builder
                ->add('images', 'entity', array(
                    'class' => 'LittleBigJoeCoreBundle:ProjectImage',
                    'expanded' => true,
                    'multiple' => true,
                    'required' => false
                ))
                ->add('videos', 'entity', array(
                    'class' => 'LittleBigJoeCoreBundle:ProjectVideo',
                    'expanded' => true,
                    'multiple' => true,
                    'required' => false
                ))
                ->add('description', 'ckeditor', array(
                    'label' => 'Description',
                    'language' => $ckeditorLanguage,
                    'width' => '100%'
                ));
                break;

            // Step 3 : Define my goals
            case 3: $builder
                ->add('amountRequired', 'text', array(
                    'label' => 'Amount to raise',
                    'mapped' => false
                ))
                ->add('endingAt', 'date', array(
                    'label' => 'Product ending at',
                    'widget' => 'single_text',
                    'format' => $format,
                    'attr' => array('class' => 'form-control datepicker'),
                ));
                break;

            // Step 4 : Choose awards
            case 4: $builder
                ->add('rewards', 'collection', array(
                    'type' => new ProjectRewardType(),
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'mapped' => false
                ))
                ->add('giftProduct', 'choice', array(
                    'label' => 'Project owner gift',
                    'choices' => array('1' => 'Offer the product to the project owner if the amount to raise is reached', '0' => 'Offer an percentage of raised funds to the project owner')
                ))
                ->add('giftPercentageFundsRaised', 'integer', array(
                    'label' => '',
                    'required' => false
                ));
                break;

            // Step 5 : Getting online
            case 5: break;

            default: break;
        }
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LittleBigJoe\Bundle\CoreBundle\Entity\ProjectProduct',
            'flow_step' => null,
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'createProduct';
    }
}