<?php

namespace LittleBigJoe\Bundle\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use LittleBigJoe\Bundle\FrontendBundle\Entity\Brand;
use LittleBigJoe\Bundle\BackendBundle\Form\BrandType;

/**
 * Brand controller.
 *
 * @Route("/brands")
 */
class BrandController extends Controller
{

    /**
     * Lists all Brand entities.
     *
     * @Route("/", name="littlebigjoe_backendbundle_brands")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $dql = "SELECT b FROM LittleBigJoeFrontendBundle:Brand b";
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
     * Creates a new Brand entity.
     *
     * @Route("/", name="littlebigjoe_backendbundle_brands_create")
     * @Method("POST")
     * @Template("LittleBigJoeBackendBundle:Brand:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Brand();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);

            if ($entity->getLogo() != null) {
                $evm = $em->getEventManager();
                $uploadableManager = $this->get('stof_doctrine_extensions.uploadable.manager');
                $evm->removeEventListener(array('postFlush'), $uploadableManager->getUploadableListener());
                $uploadableManager->markEntityToUpload($entity, $entity->getLogo());
            }

            $em->flush();

            return $this->redirect($this->generateUrl('littlebigjoe_backendbundle_brands_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Brand entity.
     *
     * @param Brand $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Brand $entity)
    {
        $form = $this->createForm(new BrandType(), $entity, array(
            'action' => $this->generateUrl('littlebigjoe_backendbundle_brands_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Brand entity.
     *
     * @Route("/new", name="littlebigjoe_backendbundle_brands_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Brand();
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a Brand entity.
     *
     * @Route("/{id}", name="littlebigjoe_backendbundle_brands_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LittleBigJoeFrontendBundle:Brand')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Brand entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Brand entity.
     *
     * @Route("/{id}/edit", name="littlebigjoe_backendbundle_brands_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LittleBigJoeFrontendBundle:Brand')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Brand entity.');
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
     * Creates a form to edit a Brand entity.
     *
     * @param Brand $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Brand $entity)
    {
        $form = $this->createForm(new BrandType(), $entity, array(
            'action' => $this->generateUrl('littlebigjoe_backendbundle_brands_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Brand entity.
     *
     * @Route("/{id}", name="littlebigjoe_backendbundle_brands_update")
     * @Method("PUT")
     * @Template("LittleBigJoeBackendBundle:Brand:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LittleBigJoeFrontendBundle:Brand')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Brand entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            if ($entity->getLogo() != null) {
                $evm = $em->getEventManager();
                $uploadableManager = $this->get('stof_doctrine_extensions.uploadable.manager');
                $evm->removeEventListener(array('postFlush'), $uploadableManager->getUploadableListener());
                $uploadableManager->markEntityToUpload($entity, $entity->getLogo());
            }

            $em->flush();

            return $this->redirect($this->generateUrl('littlebigjoe_backendbundle_brands_edit', array('id' => $id)));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Brand entity.
     *
     * @Route("/{id}", name="littlebigjoe_backendbundle_brands_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LittleBigJoeFrontendBundle:Brand')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Brand entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('littlebigjoe_backendbundle_brands'));
    }

    /**
     * Creates a form to delete a Brand entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('littlebigjoe_backendbundle_brands_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }
}
