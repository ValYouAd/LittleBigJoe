<?php

namespace LittleBigJoe\Bundle\FrontendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EntryType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    		if ($options['data']->getProject()->getUser() == null)
	    	{
	    			$ckeditorLanguage = 'en';
	    	}
	    	else
	    	{
	    			$ckeditorLanguage = $options['data']->getProject()->getUser()->getDefaultLanguage();
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
            ->add('title', 'text', array(
        				'label' => 'Entry title'
        		))
            ->add('project', 'hidden', array(
        				'data' => $options['data']->getProject()->getId()
        		))
            ->add('content', 'ckeditor', array(
			      		'label' => 'Comment content',
			      		'data' => '',
            		'language' => $ckeditorLanguage,
			      		'custom_config' => "toolbarGroups: [{ name: 'clipboard', groups: ['clipboard']}, { name: 'basicstyles', groups: ['basicstyles', 'cleanup'] }, { name: 'links' }, { name: 'styles' }]"
    				))
            ->add('isPublic', 'choice', array(
        				'label' => 'Visible to non participants ?',
            		'choices' => array('1' => 'Yes', '0' => 'No')
        		))
        		->add('addEntry', 'button', array(
            		'label' => 'Add entry to this project',
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
            'data_class' => 'LittleBigJoe\Bundle\CoreBundle\Entity\Entry'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'entry';
    }
}
