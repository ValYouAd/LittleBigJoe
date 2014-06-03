<?php

namespace LittleBigJoe\Bundle\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use LittleBigJoe\Bundle\CoreBundle\Entity\ProductType;
use LittleBigJoe\Bundle\BackendBundle\Form\ProductTypeType;

/**
 * ProductType controller.
 *
 * @Route("/product-types")
 */
class ProductTypeController extends Controller
{

    /**
     * Lists all ProductType entities.
     *
     * @Route("/", name="littlebigjoe_backendbundle_product_types")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $dql = "SELECT tp FROM LittleBigJoeCoreBundle:ProductType tp";
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
     * Creates a new ProductType entity.
     *
     * @Route("/", name="littlebigjoe_backendbundle_product_types_create")
     * @Method("POST")
     * @Template("LittleBigJoeBackendBundle:ProductType:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new ProductType();

        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('littlebigjoe_backendbundle_product_types_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a form to create a ProductType entity.
     *
     * @param ProductType $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ProductType $entity)
    {
        $form = $this->createForm(new ProductTypeType(), $entity, array(
            'action' => $this->generateUrl('littlebigjoe_backendbundle_product_types_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new ProductType entity.
     *
     * @Route("/new", name="littlebigjoe_backendbundle_product_types_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new ProductType();
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a ProductType entity.
     *
     * @Route("/{id}", name="littlebigjoe_backendbundle_product_types_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LittleBigJoeCoreBundle:ProductType')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ProductType entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing ProductType entity.
     *
     * @Route("/{id}/edit", name="littlebigjoe_backendbundle_product_types_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LittleBigJoeCoreBundle:ProductType')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ProductType entity.');
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
     * Creates a form to edit a ProductType entity.
     *
     * @param ProductType $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(ProductType $entity)
    {
        $form = $this->createForm(new ProductTypeType(), $entity, array(
            'action' => $this->generateUrl('littlebigjoe_backendbundle_product_types_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing ProductType entity.
     *
     * @Route("/{id}", name="littlebigjoe_backendbundle_product_types_update")
     * @Method("PUT")
     * @Template("LittleBigJoeBackendBundle:ProductType:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LittleBigJoeCoreBundle:ProductType')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ProductType entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('littlebigjoe_backendbundle_product_types_edit', array('id' => $id)));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a ProductType entity.
     *
     * @Route("/{id}", name="littlebigjoe_backendbundle_product_types_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LittleBigJoeCoreBundle:ProductType')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find ProductType entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('littlebigjoe_backendbundle_product_types'));
    }

    /**
     * Creates a form to delete a ProductType entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('littlebigjoe_backendbundle_product_types_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }
}
