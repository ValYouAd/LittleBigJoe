<?php

namespace LittleBigJoe\Bundle\FrontendBundle\Form;

use Craue\FormFlowBundle\Form\FormFlow;
use Doctrine\ORM\EntityManager;
use LittleBigJoe\Bundle\CoreBundle\Entity\ProductType;
use Symfony\Component\Form\FormTypeInterface;
use Craue\FormFlowBundle\Event\PostBindFlowEvent;
use Craue\FormFlowBundle\Form\FormFlowEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EditProductFlow extends FormFlow implements EventSubscriberInterface
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
        return 'editProduct';
    }

    protected function loadStepsConfig()
    {
        return array(
            array(
                'label' => 'Edit the product',
                'type' => $this->formType,
            ),
            array(
                'label' => 'Present the product',
                'type' => $this->formType,
            ),
            array(
                'label' => 'Define the goals',
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
        $formData = $this->getRequest()->request->get('editProduct', null);
        $projectFields = $this->getRequest()->getSession()->get('projectFields');
        $projectFieldsKeys = array('amountRequired', 'rewards');

        if (!empty($formData))
        {
            foreach ($formData as $key => $formRow)
            {
                if (in_array($key, $projectFieldsKeys))
                {
                    $projectFields[$key] = $formRow;
                }
            }
        }

        $this->getRequest()->getSession()->set('projectFields', $projectFields);
    }
}