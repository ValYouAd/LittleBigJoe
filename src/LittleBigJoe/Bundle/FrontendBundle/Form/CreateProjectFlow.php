<?php

namespace LittleBigJoe\Bundle\FrontendBundle\Form;

use Craue\FormFlowBundle\Form\FormFlow;
use Craue\FormFlowBundle\Form\FormFlowInterface;
use Symfony\Component\Form\FormTypeInterface;

class CreateProjectFlow extends FormFlow 
{
		/**
		 * @var FormTypeInterface
		 */
		protected $formType;
		
		protected $allowDynamicStepNavigation = true;	
	
		public function setFormType(FormTypeInterface $formType) 
		{
				$this->formType = $formType;
		}
	
		public function getName() 
		{
				return 'createProject';
		}
	
		protected function loadStepsConfig() 
		{
				return array(
						array(
								'label' => 'Create my project',
								'type' => $this->formType,
						),
						array(
								'label' => 'Define my goals',
								'type' => $this->formType,
						),
						array(
								'label' => 'Present my project',
								'type' => $this->formType,
						),
						array(
								'label' => 'Choose awards',
								'type' => $this->formType,
						),
						array(
								'label' => 'Getting online',
								'type' => $this->formType,
						)
				);
		}
		
		/**
		 * {@inheritDoc}
		 */
		public function getFormOptions($step, array $options = array()) 
		{
				$options = parent::getFormOptions($step, $options);
			
				$options['cascade_validation'] = true;
				$options['flow_step'] = $step;
			
				return $options;
		}
}