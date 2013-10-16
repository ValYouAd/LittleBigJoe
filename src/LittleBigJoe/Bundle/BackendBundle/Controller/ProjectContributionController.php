<?php

namespace LittleBigJoe\Bundle\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use LittleBigJoe\Bundle\CoreBundle\Entity\ProjectContribution;
use LittleBigJoe\Bundle\BackendBundle\Form\ProjectContributionType;

/**
 * ProjectContribution controller.
 *
 * @Route("/contributions")
 */
class ProjectContributionController extends Controller
{

    /**
     * Lists all ProjectContribution entities.
     *
     * @Route("/", name="littlebigjoe_backendbundle_contributions")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $dql = "SELECT pc FROM LittleBigJoeCoreBundle:ProjectContribution pc";
        $query = $em->createQuery($dql);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1),
            $this->container->getParameter('nb_elements_by_page')
        );

        return array(
            'pagination' => $pagination
        );
    }

    /**
     * Creates a new ProjectContribution entity.
     *
     * @Route("/", name="littlebigjoe_backendbundle_contributions_create")
     * @Method("POST")
     * @Template("LittleBigJoeCoreBundle:ProjectContribution:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new ProjectContribution();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('littlebigjoe_backendbundle_contributions_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a form to create a ProjectContribution entity.
     *
     * @param ProjectContribution $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ProjectContribution $entity)
    {
        $form = $this->createForm(new ProjectContributionType(), $entity, array(
            'action' => $this->generateUrl('littlebigjoe_backendbundle_contributions_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new ProjectContribution entity.
     *
     * @Route("/new", name="littlebigjoe_backendbundle_contributions_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new ProjectContribution();
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a ProjectContribution entity.
     *
     * @Route("/{id}", name="littlebigjoe_backendbundle_contributions_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LittleBigJoeCoreBundle:ProjectContribution')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ProjectContribution entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing ProjectContribution entity.
     *
     * @Route("/{id}/edit", name="littlebigjoe_backendbundle_contributions_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LittleBigJoeCoreBundle:ProjectContribution')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ProjectContribution entity.');
        }

        $editForm = $this->createEditForm($entity);
        $refundForm = $this->createRefundForm($id);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
        		'refund_form' => $refundForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Creates a form to edit a ProjectContribution entity.
     *
     * @param ProjectContribution $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(ProjectContribution $entity)
    {
        $form = $this->createForm(new ProjectContributionType(), $entity, array(
            'action' => $this->generateUrl('littlebigjoe_backendbundle_contributions_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing ProjectContribution entity.
     *
     * @Route("/{id}", name="littlebigjoe_backendbundle_contributions_update")
     * @Method("PUT")
     * @Template("LittleBigJoeCoreBundle:ProjectContribution:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LittleBigJoeCoreBundle:ProjectContribution')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ProjectContribution entity.');
        }

        $refundForm = $this->createRefundForm($id);
        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('littlebigjoe_backendbundle_contributions_edit', array('id' => $id)));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
        		'refund_form' => $refundForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a ProjectContribution entity.
     *
     * @Route("/{id}", name="littlebigjoe_backendbundle_contributions_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LittleBigJoeCoreBundle:ProjectContribution')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find ProjectContribution entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('littlebigjoe_backendbundle_contributions'));
    }

    /**
     * Creates a form to delete a ProjectContribution entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('littlebigjoe_backendbundle_contributions_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }
    

    /**
     * Refunds a ProjectContribution entity.
     *
     * @Route("/{id}", name="littlebigjoe_backendbundle_contributions_refund")
     * @Method("POST")
     */
    public function refundAction(Request $request, $id)
    {
    		$api = $this->container->get('little_big_joe_mango_pay.api');
    		$currentUser = $this->get('security.context')->getToken()->getUser();
	    	$form = $this->createRefundForm($id);
	    	$form->handleRequest($request);
	    
	    	if ($form->isValid()) {
		    		$em = $this->getDoctrine()->getManager();
		    		$entity = $em->getRepository('LittleBigJoeCoreBundle:ProjectContribution')->find($id);
		    
		    		if (!$entity) {
		    				throw $this->createNotFoundException('Unable to find ProjectContribution entity.');
		    		}
		    
		    		// Make the refund request
		    		$mangopayRefund = $api->createRefund($entity->getMangopayContributionId(), $currentUser->getMangopayUserId());
		    		var_dump($mangopayRefund);
		    		if (!empty($mangopayRefund) && !empty($entity))
		    		{
		    				if (!empty($mangopayRefund->ID) && $mangopayRefund->IsSucceeded && $mangopayRefund->IsCompleted)
		    				{	
				    				$entity->setIsRefunded(true);
				    				$entity->setMangopayRefundId($mangopayRefund->ID);
				    				
				    				$em->persist($entity);
				    				$em->flush();
				    				
				    				// Send contribution refund email
				    				$email = \Swift_Message::newInstance()
									    				->setContentType('text/html')
									    				->setSubject($this->container->get('translator')->trans('You\'ve been refund for your contribution'))
									    				->setFrom($this->container->getParameter('default_email_address'))
									    				->setTo(array($entity->getUser()->getEmail() => $entity->getUser()))
									    				->setBody(
									    						$this->container->get('templating')->render('LittleBigJoeFrontendBundle:Email:contribution_refund.html.twig', array(
									    								'user' => $entity->getUser(),
									    								'contribution' => $entity,
									    								'url' => $this->container->get('request')->getSchemeAndHttpHost()
									    						), 'text/html')
									    				);
				    				$this->container->get('mailer')->send($email);
				    				
				    				return $this->redirect($this->generateUrl('littlebigjoe_backendbundle_contributions_show', array('id' => $id)));
		    				}		    				
		    		}
		    }
	    	
	    	return $this->redirect($this->generateUrl('littlebigjoe_backendbundle_contributions'));
    }
    
    /**
     * Creates a form to refund a ProjectContribution entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createRefundForm($id)
    {
	    	return $this->createFormBuilder()
			    	->setAction($this->generateUrl('littlebigjoe_backendbundle_contributions_refund', array('id' => $id)))
			    	->setMethod('POST')
			    	->add('submit', 'submit', array('label' => 'Refund'))
			    	->getForm();
    }
}
