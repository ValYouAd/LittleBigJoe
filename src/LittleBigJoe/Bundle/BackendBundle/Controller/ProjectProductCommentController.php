<?php

namespace LittleBigJoe\Bundle\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use LittleBigJoe\Bundle\CoreBundle\Entity\ProjectProductComment;
use LittleBigJoe\Bundle\BackendBundle\Form\ProjectProductCommentType;

/**
 * ProjectProductComment controller.
 *
 * @Route("/project-product-comments")
 */
class ProjectProductCommentController extends Controller
{

    /**
     * Lists all ProjectProductComment entities.
     *
     * @Route("/", name="littlebigjoe_backendbundle_project_product_comments")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $dql = "SELECT p FROM LittleBigJoeCoreBundle:ProjectProductComment p";
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
     *
     * Creates a new ProjectProductComment entity.
     *
     * @Route("/", name="littlebigjoe_backendbundle_project_product_comments_create")
     * @Method("POST")
     * @Template("LittleBigJoeCoreBundle:ProjectProductComment:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new ProjectProductComment();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('littlebigjoe_backendbundle_project_product_comments_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a ProjectProductComment entity.
    *
    * @param ProjectProductComment $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(ProjectProductComment $entity)
    {
        $form = $this->createForm(new ProjectProductCommentType(), $entity, array(
            'action' => $this->generateUrl('littlebigjoe_backendbundle_project_product_comments_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new ProjectProductComment entity.
     *
     * @Route("/new", name="littlebigjoe_backendbundle_project_product_comments_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new ProjectProductComment();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a ProjectProductComment entity.
     *
     * @Route("/{id}", name="littlebigjoe_backendbundle_project_product_comments_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LittleBigJoeCoreBundle:ProjectProductComment')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ProjectProductComment entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing ProjectProductComment entity.
     *
     * @Route("/{id}/edit", name="littlebigjoe_backendbundle_project_product_comments_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LittleBigJoeCoreBundle:ProjectProductComment')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ProjectProductComment entity.');
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
    * Creates a form to edit a ProjectProductComment entity.
    *
    * @param ProjectProductComment $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(ProjectProductComment $entity)
    {
        $form = $this->createForm(new ProjectProductCommentType(), $entity, array(
            'action' => $this->generateUrl('littlebigjoe_backendbundle_project_product_comments_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing ProjectProductComment entity.
     *
     * @Route("/{id}", name="littlebigjoe_backendbundle_project_product_comments_update")
     * @Method("PUT")
     * @Template("LittleBigJoeCoreBundle:ProjectProductComment:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LittleBigJoeCoreBundle:ProjectProductComment')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ProjectProductComment entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('littlebigjoe_backendbundle_project_product_comments_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a ProjectProductComment entity.
     *
     * @Route("/{id}", name="littlebigjoe_backendbundle_project_product_comments_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LittleBigJoeCoreBundle:ProjectProductComment')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find ProjectProductComment entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('littlebigjoe_backendbundle_project_product_comments'));
    }

    /**
     * Creates a form to delete a ProjectProductComment entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('littlebigjoe_backendbundle_project_product_comments_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
