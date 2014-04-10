<?php

namespace LittleBigJoe\Bundle\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use LittleBigJoe\Bundle\CoreBundle\Entity\ProjectProduct;
use LittleBigJoe\Bundle\BackendBundle\Form\ProjectProductType;

/**
 * ProjectProduct controller.
 *
 * @Route("/project-products")
 */
class ProjectProductController extends Controller
{

    /**
     * Lists all ProjectProduct entities.
     *
     * @Route("/", name="littlebigjoe_backendbundle_project_products")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $dql = "SELECT p FROM LittleBigJoeCoreBundle:ProjectProduct p";
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
     * Creates a new ProjectProduct entity.
     *
     * @Route("/", name="littlebigjoe_backendbundle_project_products_create")
     * @Method("POST")
     * @Template("LittleBigJoeBackendBundle:ProjectProduct:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new ProjectProduct();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('littlebigjoe_backendbundle_project_products_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a ProjectProduct entity.
    *
    * @param ProjectProduct $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(ProjectProduct $entity)
    {
        $form = $this->createForm(new ProjectProductType(), $entity, array(
            'action' => $this->generateUrl('littlebigjoe_backendbundle_project_products_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new ProjectProduct entity.
     *
     * @Route("/new", name="littlebigjoe_backendbundle_project_products_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new ProjectProduct();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a ProjectProduct entity.
     *
     * @Route("/{id}", name="littlebigjoe_backendbundle_project_products_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LittleBigJoeCoreBundle:ProjectProduct')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ProjectProduct entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing ProjectProduct entity.
     *
     * @Route("/{id}/edit", name="littlebigjoe_backendbundle_project_products_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LittleBigJoeCoreBundle:ProjectProduct')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ProjectProduct entity.');
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
    * Creates a form to edit a ProjectProduct entity.
    *
    * @param ProjectProduct $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(ProjectProduct $entity)
    {
        $form = $this->createForm(new ProjectProductType(), $entity, array(
            'action' => $this->generateUrl('littlebigjoe_backendbundle_project_products_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing ProjectProduct entity.
     *
     * @Route("/{id}", name="littlebigjoe_backendbundle_project_products_update")
     * @Method("PUT")
     * @Template("LittleBigJoeBackendBundle:ProjectProduct:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LittleBigJoeCoreBundle:ProjectProduct')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ProjectProduct entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('littlebigjoe_backendbundle_project_products_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a ProjectProduct entity.
     *
     * @Route("/{id}", name="littlebigjoe_backendbundle_project_products_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LittleBigJoeCoreBundle:ProjectProduct')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find ProjectProduct entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('littlebigjoe_backendbundle_project_products'));
    }

    /**
     * Creates a form to delete a ProjectProduct entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('littlebigjoe_backendbundle_project_products_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
