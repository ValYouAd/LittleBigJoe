<?php

namespace LittleBigJoe\Bundle\FrontendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use LittleBigJoe\Bundle\CoreBundle\Entity\User;

class EntryCommentType extends AbstractType
{
		public function __construct($options) 
		{
				$this->options = $options;
		}
				
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
	    	$builder
			    	->add('project', 'hidden', array(
			    			'data' => $this->options['project']->getId(),
			    			'mapped' => false
			    	))
        		->add('entry', 'entity', array(
        				'label' => 'Associated entry',
        				'class' => 'LittleBigJoeCoreBundle:Entry',
        				'query_builder' => function (EntityRepository $er) {
	        					return $er->createQueryBuilder('e')
					        					->where('e.project = :project')
					        					->setParameter('project', $this->options['project'])
					        					->orderBy('e.createdAt', 'DESC');
        				}
        		))
            ->add('content', 'textarea', array(
			      		'label' => false,
			      		'data' => '',))
    				->add('addEntryComment', 'button', array(
    						'label' => 'Comment this entry',
    						'attr' => array(
    								'class' => 'btn btn-success'
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
            'data_class' => 'LittleBigJoe\Bundle\CoreBundle\Entity\EntryComment'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'entrycomment';
    }
}
