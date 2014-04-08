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
    		if (!($this->options['user'] instanceof User))
	    	{
	    			$ckeditorLanguage = 'en';
	    	}
	    	else
	    	{
		    		$ckeditorLanguage = $this->options['user']->getDefaultLanguage();
		    		if (empty($ckeditorLanguage))
		    		{
		    				$ckeditorLanguage = 'en';
		    		}
	    	}
	    	
	    	// Define default language for CKEditor interface
	    	switch ($ckeditorLanguage)
	    	{
		    		case 'en': $ckeditorLanguage = 'en-US';
		    							 break;
		    		case 'fr': $ckeditorLanguage = 'fr-FR';
		    							 break;
		    		default: 	 $ckeditorLanguage = 'en-US';
		    							 break;
	    	}
    	
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
            ->add('content', 'ckeditor', array(
			      		'label' => 'Comment content',
			      		'data' => '',
                        'language' => $ckeditorLanguage,
                        'width' => '100%',
			      		'custom_config' => "toolbarGroups: [{ name: 'clipboard', groups: ['clipboard']}, { name: 'basicstyles', groups: ['basicstyles', 'cleanup'] }, { name: 'links' }, { name: 'styles' }]"
    				))
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
