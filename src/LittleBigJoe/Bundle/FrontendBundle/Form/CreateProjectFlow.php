<?php

namespace LittleBigJoe\Bundle\FrontendBundle\Form;

use Craue\FormFlowBundle\Form\FormFlow;
use Doctrine\ORM\EntityManager;
use LittleBigJoe\Bundle\CoreBundle\Entity\Brand;
use LittleBigJoe\Bundle\CoreBundle\Entity\ProductType;
use Symfony\Component\Form\FormTypeInterface;
use Craue\FormFlowBundle\Event\PostBindFlowEvent;
use Craue\FormFlowBundle\Form\FormFlowEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CreateProjectFlow extends FormFlow implements EventSubscriberInterface
{
		/**
		 * @var FormTypeInterface
		 */
		protected $formType;
        protected $entityManager;
		
		protected $allowDynamicStepNavigation = true;	

        public function setEntityManager(EntityManager $entityManager)
        {
            $this->entityManager = $entityManager;
        }

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
								'label' => 'Present my project',
								'type' => $this->formType,
						),
                        array(
                            'label' => 'Define my goals',
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
            $options['locale'] = $this->getRequest()->getLocale();
			
				return $options;
		}

    public function setEventDispatcher(EventDispatcherInterface $dispatcher)
    {
        parent::setEventDispatcher($dispatcher);
        $dispatcher->addSubscriber($this);
    }

    public static function getSubscribedEvents() {
        return array(
            FormFlowEvents::POST_BIND_FLOW => 'onPostBindFlow'
        );
    }

    public function onPostBindFlow(PostBindFlowEvent $event) {
        $formData = $this->getRequest()->request->get('createProject', null);

        // Create or load brand entity dynamically, after step change
        if (!empty($formData['brand']))
        {
            $brandEntity = $this->entityManager->getRepository('LittleBigJoeCoreBundle:Brand')->findOneBy(array(
                'name' => $formData['brand'],
            ));

            if (!($brandEntity instanceof Brand))
            {
                $brandEntity = new Brand();
                $brandEntity->setName(ucwords($formData['brand']));

                $this->entityManager->persist($brandEntity);
                $this->entityManager->flush();
            }

            $formData['brand'] = $brandEntity;
            $this->getRequest()->request->set('createProject', $formData);
        }

        // Create or load product type entity dynamically, after step change
        if (!empty($formData['productType']))
        {
            $productTypeEntity = $this->entityManager->getRepository('LittleBigJoeCoreBundle:ProductType')->findOneBy(array(
                'name' => $formData['productType'],
                'language' => $this->getRequest()->getLocale()
            ));

            if (!($productTypeEntity instanceof ProductType))
            {
                $productTypeEntity = new ProductType();
                $productTypeEntity->setName(ucwords($formData['productType']));
                $productTypeEntity->setIsVisible(true);
                $productTypeEntity->setLanguage($this->getRequest()->getLocale());

                $this->entityManager->persist($productTypeEntity);
                $this->entityManager->flush();
            }

            $formData['productType'] = $productTypeEntity;
            $this->getRequest()->request->set('createProject', $formData);
        }

        // Add target="blank" attribute to all links, after step change
        if (!empty($formData['description']))
        {
            $formData['description'] = preg_replace("/<a(.*?)>/", "<a$1 target=\"_blank\">", $formData['description']);
            $this->getRequest()->request->set('createProject', $formData);
        }
    }
}