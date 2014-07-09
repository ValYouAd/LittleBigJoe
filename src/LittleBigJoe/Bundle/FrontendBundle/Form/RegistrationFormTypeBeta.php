<?php

namespace LittleBigJoe\Bundle\FrontendBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use LittleBigJoe\Bundle\FrontendBundle\Form\RegistrationFormType as BaseType;
use Symfony\Component\Translation\Translator;

class RegistrationFormTypeBeta extends BaseType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $translator = new Translator('fr_FR');
        $builder->add('betaCodeValue', 'text', array('label' => $translator->trans('Beta code'), 'required' => false));
    }

    public function getName()
    {
        return 'littlebigjoe_bundle_frontendbundle_user_registration_beta';
    }
}