<?php

namespace LittleBigJoe\Bundle\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use LittleBigJoe\Bundle\FrontendBundle\Entity\Faq;
use LittleBigJoe\Bundle\BackendBundle\Form\FaqType;

/**
 * Faq controller.
 *
 * @Route("/faqs")
 */
class FaqController extends Controller
{

    /**
     * Lists all Faq entities.
     *
     * @Route("/", name="littlebigjoe_backendbundle_faqs")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $dql = "SELECT f FROM LittleBigJoeFrontendBundle:Faq f";
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
     * Creates a new Faq entity.
     *
     * @Route("/", name="littlebigjoe_backendbundle_faqs_create")
     * @Method("POST")
     * @Template("LittleBigJoeFrontendBundle:Faq:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Faq();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('littlebigjoe_backendbundle_faqs_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Faq entity.
     *
     * @param Faq $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Faq $entity)
    {
        $form = $this->createForm(new FaqType(), $entity, array(
            'action' => $this->generateUrl('littlebigjoe_backendbundle_faqs_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Faq entity.
     *
     * @Route("/new", name="littlebigjoe_backendbundle_faqs_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Faq();
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a Faq entity.
     *
     * @Route("/{id}", name="littlebigjoe_backendbundle_faqs_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LittleBigJoeFrontendBundle:Faq')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Faq entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Faq entity.
     *
     * @Route("/{id}/edit", name="littlebigjoe_backendbundle_faqs_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LittleBigJoeFrontendBundle:Faq')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Faq entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Creates a form to edit a Faq entity.
     *
     * @param Faq $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Faq $entity)
    {
        $form = $this->createForm(new FaqType(), $entity, array(
            'action' => $this->generateUrl('littlebigjoe_backendbundle_faqs_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Faq entity.
     *
     * @Route("/{id}", name="littlebigjoe_backendbundle_faqs_update")
     * @Method("PUT")
     * @Template("LittleBigJoeFrontendBundle:Faq:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LittleBigJoeFrontendBundle:Faq')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Faq entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('littlebigjoe_backendbundle_faqs_edit', array('id' => $id)));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Faq entity.
     *
     * @Route("/{id}", name="littlebigjoe_backendbundle_faqs_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LittleBigJoeFrontendBundle:Faq')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Faq entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('littlebigjoe_backendbundle_faqs'));
    }

    /**
     * Creates a form to delete a Faq entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('littlebigjoe_backendbundle_faqs_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }
}
