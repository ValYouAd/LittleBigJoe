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
        if (empty($this->options['project']))
        {
            $projectData = '';
        }
        else
        {
            $projectData = $this->options['project']->getId();
        }

        if (empty($this->options['entry']))
        {
            $entryData = '';
        }
        else
        {
            $entryData = $this->options['entry']->getId();
        }

        $builder
            ->add('project', 'hidden', array(
                'data' => $projectData,
                'mapped' => false,
            ))
            ->add('entry', 'hidden', array(
                'data' => $entryData,
                'mapped' => false,
            ))
            ->add('content', 'textarea', array(
                'label' => false,
                'data' => '',
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
            'data_class' => 'LittleBigJoe\Bundle\CoreBundle\Entity\EntryComment',
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
