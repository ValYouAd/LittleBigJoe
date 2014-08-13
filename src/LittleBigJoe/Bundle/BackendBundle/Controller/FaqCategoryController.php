<?php

namespace LittleBigJoe\Bundle\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use LittleBigJoe\Bundle\CoreBundle\Entity\FaqCategory;
use LittleBigJoe\Bundle\BackendBundle\Form\FaqCategoryType;

/**
 * FaqCategory controller.
 *
 * @Route("/categories/faqs")
 */
class FaqCategoryController extends Controller
{

    /**
     * Lists all FaqCategory entities.
     *
     * @Route("/", name="littlebigjoe_backendbundle_faq_categories")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $dql = "SELECT f FROM LittleBigJoeCoreBundle:FaqCategory f";
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
     * Creates a new FaqCategory entity.
     *
     * @Route("/", name="littlebigjoe_backendbundle_faq_categories_create")
     * @Method("POST")
     * @Template("LittleBigJoeCoreBundle:FaqCategory:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new FaqCategory();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('littlebigjoe_backendbundle_faq_categories_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a form to create a FaqCategory entity.
     *
     * @param FaqCategory $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(FaqCategory $entity)
    {
        $form = $this->createForm(new FaqCategoryType(), $entity, array(
            'action' => $this->generateUrl('littlebigjoe_backendbundle_faq_categories_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new FaqCategory entity.
     *
     * @Route("/new", name="littlebigjoe_backendbundle_faq_categories_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new FaqCategory();
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a FaqCategory entity.
     *
     * @Route("/{id}", name="littlebigjoe_backendbundle_faq_categories_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LittleBigJoeCoreBundle:FaqCategory')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FaqCategory entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing FaqCategory entity.
     *
     * @Route("/{id}/edit", name="littlebigjoe_backendbundle_faq_categories_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LittleBigJoeCoreBundle:FaqCategory')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FaqCategory entity.');
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
     * Creates a form to edit a FaqCategory entity.
     *
     * @param FaqCategory $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(FaqCategory $entity)
    {
        $form = $this->createForm(new FaqCategoryType(), $entity, array(
            'action' => $this->generateUrl('littlebigjoe_backendbundle_faq_categories_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing FaqCategory entity.
     *
     * @Route("/{id}", name="littlebigjoe_backendbundle_faq_categories_update")
     * @Method("PUT")
     * @Template("LittleBigJoeCoreBundle:FaqCategory:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LittleBigJoeCoreBundle:FaqCategory')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FaqCategory entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('littlebigjoe_backendbundle_faq_categories_edit', array('id' => $id)));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a FaqCategory entity.
     *
     * @Route("/{id}", name="littlebigjoe_backendbundle_faq_categories_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LittleBigJoeCoreBundle:FaqCategory')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find FaqCategory entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('littlebigjoe_backendbundle_faq_categories'));
    }

    /**
     * Creates a form to delete a FaqCategory entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('littlebigjoe_backendbundle_faq_categories_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }
}
