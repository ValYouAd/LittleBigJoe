<?php

namespace LittleBigJoe\Bundle\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProjectVideoType extends AbstractType
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
            ->add('description', 'textarea', array(
                'label' => 'backend.description'
            ))
            ->add('providerName', 'choice', array(
                'label' => 'backend.provider_name',
                'choices' => array('YouTube' => 'backend.youtube', 'DailyMotion' => 'backend.dailymotion', 'Vimeo' => 'backend.vimeo')
            ))
            ->add('providerVideoId', 'text', array(
                'label' => 'backend.provider_video_id'
            ))
            ->add('embedPlayerCode', 'textarea', array(
                'label' => 'backend.embed_player_code',
                'required' => false,
            ))
            ->add('thumbWidth', 'integer', array(
                'label' => 'backend.thumb_width'
            ))
            ->add('thumbHeight', 'integer', array(
                'label' => 'backend.thumb_height'
            ))
            ->add('thumbUrl', 'url', array(
                'label' => 'backend.thumb_url'
            ))
            ->add('highlighted', 'checkbox', array(
                'label' => 'backend.highlighted',
                'required' => false
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LittleBigJoe\Bundle\CoreBundle\Entity\ProjectVideo'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'littlebigjoe_bundle_backendbundle_projectvideo';
    }
}
