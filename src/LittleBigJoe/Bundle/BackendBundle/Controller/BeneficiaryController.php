<?php

namespace LittleBigJoe\Bundle\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use LittleBigJoe\Bundle\CoreBundle\Entity\Beneficiary;
use LittleBigJoe\Bundle\BackendBundle\Form\BeneficiaryType;

/**
 * Beneficiary controller.
 *
 * @Route("/beneficiaries")
 */
class BeneficiaryController extends Controller
{
    /**
     * Lists all Beneficiary entities.
     *
     * @Route("/", name="littlebigjoe_backendbundle_beneficiaries")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $dql = "SELECT b FROM LittleBigJoeCoreBundle:Beneficiary b";
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
     * Creates a new Beneficiary entity.
     *
     * @Route("/", name="littlebigjoe_backendbundle_beneficiaries_create")
     * @Method("POST")
     * @Template("LittleBigJoeBackendBundle:Beneficiary:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Beneficiary();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            // Create beneficiary in MangoPay
            $api = $this->container->get('little_big_joe_mango_pay.api');
            $mangopayBeneficiary = $api->createBeneficiary($entity->getBankAccountOwnerName(), $entity->getBankAccountOwnerAddress(), $entity->getBankAccountIban(), $entity->getBankAccountBic(), $entity->getId(), (($entity->getUser()) ? $entity->getUser()->getId() : null));
            if (!empty($mangopayBeneficiary))
            {
	            	if (!empty($mangopayBeneficiary->ID))
	            	{
	            			$entity->setMangopayBeneficiaryId($mangopayBeneficiary->ID);
	            	}
	            	$em->persist($entity);
	            	$em->flush();
            }
            
            return $this->redirect($this->generateUrl('littlebigjoe_backendbundle_beneficiaries_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a Beneficiary entity.
    *
    * @param Beneficiary $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Beneficiary $entity)
    {
        $form = $this->createForm(new BeneficiaryType(), $entity, array(
            'action' => $this->generateUrl('littlebigjoe_backendbundle_beneficiaries_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Beneficiary entity.
     *
     * @Route("/new", name="littlebigjoe_backendbundle_beneficiaries_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Beneficiary();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Beneficiary entity.
     *
     * @Route("/{id}", name="littlebigjoe_backendbundle_beneficiaries_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LittleBigJoeCoreBundle:Beneficiary')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Beneficiary entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Beneficiary entity.
     *
     * @Route("/{id}/edit", name="littlebigjoe_backendbundle_beneficiaries_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LittleBigJoeCoreBundle:Beneficiary')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Beneficiary entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Beneficiary entity.
    *
    * @param Beneficiary $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Beneficiary $entity)
    {
        $form = $this->createForm(new BeneficiaryType(), $entity, array(
            'action' => $this->generateUrl('littlebigjoe_backendbundle_beneficiaries_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    
    /**
     * Edits an existing Beneficiary entity.
     *
     * @Route("/{id}", name="littlebigjoe_backendbundle_beneficiaries_update")
     * @Method("PUT")
     * @Template("LittleBigJoeBackendBundle:Beneficiary:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LittleBigJoeCoreBundle:Beneficiary')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Beneficiary entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('littlebigjoe_backendbundle_beneficiaries_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    
    /**
     * Deletes a Beneficiary entity.
     *
     * @Route("/{id}", name="littlebigjoe_backendbundle_beneficiaries_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LittleBigJoeCoreBundle:Beneficiary')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Beneficiary entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('littlebigjoe_backendbundle_beneficiaries'));
    }

    /**
     * Creates a form to delete a Beneficiary entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('littlebigjoe_backendbundle_beneficiaries_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
