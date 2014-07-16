<?php

namespace LittleBigJoe\Bundle\FrontendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Constraints\Image;

class CreateProjectFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    		$ckeditorLanguage = $options['locale'];
            $brand = $options['data']->getBrand();
            if (empty($brand))
            {
                $brand = '';
            }
            $productType = $options['data']->getProductType();
            if (empty($productType))
            {
                $productType = '';
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
                    ->add('photo', 'file', array(
                            'label' => 'Logo',
                            'attr' => array(
                                    'class' => 'file',
                                'accept' => "image/*"
                            ),
                            'data_class' => null,
                            'mapped' => true,
                            'required' => false,
                    ))
                    ->add('brand', 'text', array(
                        'label' => 'Associated brand',
                        'data' => $brand
                    ))
                    ->add('productType', 'text', array(
                        'label' => 'Product type',
                        'data' => $productType
                    ))
                    ->add('location', 'text', array(
                        'label' => 'Location'
                    ))
                    ->add('categories', 'entity', array(
                        'label' => 'Associated categories',
                        'class' => 'LittleBigJoeCoreBundle:Category',
                        'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('c')
                                    ->where('c.isVisible = :isVisible')
                                    ->setParameter('isVisible', true)
                                ->orderBy('c.name', 'ASC');
                        },
                        'expanded' => false,
                        'multiple' => true,
                        'required' => true
                    ))
                    ->add('pitch', 'textarea', array(
                            'label' => 'Pitch'
                    ))
                    ->add('hasBrandRepresentation', 'choice', array(
                        'label' => 'Does the project contains a visual or written representation of the brand ?',
                        'choices' => array(0 => 'No', 1 => 'Yes')
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
                        'data' => $options['data']->getDescription(),
                        'language' => $ckeditorLanguage,
                        'width' => '100%'
                    ));
                    break;

            // Step 3 : Define my goals
            case 3: $tomorrow = new \DateTime();
                    $builder
                    ->add('likesRequired', 'text', array(
                            'label' => 'Likes to get'
                    ))
                    ->add('endingAt', 'date', array(
                        'label' => 'Project ending at',
                        'widget' => 'single_text',
                        'format' => 'MM/dd/yyyy',
                        'attr' => array('class' => 'form-control datepicker'),
                    ));
                    break;

            // Step 4 : Getting online
            case 4: break;
            
            default: break;
    		}
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LittleBigJoe\Bundle\CoreBundle\Entity\Project',
        		'flow_step' => null,
            'locale' => 'en',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'createProject';
    }
}
