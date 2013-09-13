<?php

namespace LittleBigJoe\Bundle\FrontendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class CreateProjectFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    		//var_dump($options['data']);
    	
    		switch ($options['flow_step']) 
    		{
    				// Step 1 : Create my project
            case 1: $builder
						            ->add('name', 'text', array(
						                'label' => 'Name'
						            ))
						            ->add('slug', 'text', array(
						                'label' => 'Slug'
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
						            ->add('brand', 'entity', array(
						                'label' => 'Associated brand',
						                'class' => 'LittleBigJoeFrontendBundle:Brand',
						                'property' => 'name'
						            ))
						            ->add('location', 'text', array(
						            		'label' => 'Location'
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
						            ->add('pitch', 'textarea', array(
						            		'label' => 'Pitch'
						            ));
										break;
										
            // Step 2 : Define my goals
            case 2: $builder
						            ->add('amountRequired', 'text', array(
						            		'label' => 'Amount to raise'
						            ))
						            ->add('likesRequired', 'integer', array(
						            		'label' => 'Likes to get'
						            ))
						            ->add('endingAt', 'datetime', array(
						            		'label' => 'Project ending at'
						            ));
						        break;

						// Step 3 : Present my project
            case 3: $builder
						            ->add('description', 'ckeditor', array(
						            		'label' => 'Description',
						            		'data' => $options['data']->getDescription()
						            ));
										break;
									
						// Step 4 : Choose awards
            case 4: $builder
						            ->add('rewards', 'collection', array(
						            		'type' => new ProjectRewardType(),
						            		'allow_add' => true,
										        'allow_delete' => true,
										        'by_reference' => false
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
            'data_class' => 'LittleBigJoe\Bundle\FrontendBundle\Entity\Project',
        		'flow_step' => null,
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
