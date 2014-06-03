<?php

namespace LittleBigJoe\Bundle\FrontendBundle\Form;

use Craue\FormFlowBundle\Form\FormFlow;
use Doctrine\ORM\EntityManager;
use LittleBigJoe\Bundle\CoreBundle\Entity\ProductType;
use LittleBigJoe\Bundle\CoreBundle\Entity\Project;
use Symfony\Component\Form\FormTypeInterface;
use Craue\FormFlowBundle\Event\PostBindFlowEvent;
use Craue\FormFlowBundle\Form\FormFlowEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EditProjectFlow extends FormFlow implements EventSubscriberInterface
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
        return 'editProject';
    }

    protected function loadStepsConfig()
    {
        return array(
            array(
                'label' => 'Edit my project',
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
                'label' => 'Preview',
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
        $formData = $this->getRequest()->request->get('editProject', null);

        // Create or load product type entity dynamically, after step change
        if (!empty($formData['productType']))
        {
            $productTypeEntity = $this->entityManager->getRepository('LittleBigJoeCoreBundle:ProductType')->findOneBy(array(
                'name' => $formData['productType'],
                'language' => $this->getRequest()->getLocale()
            ));

            if (!($productTypeEntity instanceof ProductType))
            {
                $project = $this->entityManager->getRepository('LittleBigJoeCoreBundle:Project')->findOneBySlug($formData['slug']);

                $productTypeEntity = new ProductType();
                $productTypeEntity->setName(ucwords($formData['productType']));
                $productTypeEntity->setIsVisible(true);
                $productTypeEntity->setLanguage($this->getRequest()->getLocale());
                $productTypeEntity->addProject($project);
                $project->setProductType($productTypeEntity);

                $this->entityManager->persist($productTypeEntity);
                $this->entityManager->flush();
            }

            $formData['productType'] = $productTypeEntity;
            $this->getRequest()->request->set('editProject', $formData);
        }

        // Add target="blank" attribute to all links, after step change
        if (!empty($formData['description']))
        {
            $formData['description'] = preg_replace("/<a(.*?)>/", "<a$1 target=\"_blank\">", $formData['description']);
            $this->getRequest()->request->set('editProject', $formData);
        }
    }
}