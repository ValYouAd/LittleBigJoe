<?php

namespace LittleBigJoe\Bundle\FrontendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use LittleBigJoe\Bundle\CoreBundle\Entity\User;

class CommentType extends AbstractType
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
        				'data' => $options['data']->getProject()->getId()
        		))
                ->add('content', 'textarea', array(
			      		'label' => false,
			      		'data' => '',
                ))
			      ->add('addComment', 'button', array(
			      		'label' => 'Comment this project',
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
            'data_class' => 'LittleBigJoe\Bundle\CoreBundle\Entity\Comment'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'comment';
    }
}
