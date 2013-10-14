<?php

namespace LittleBigJoe\Bundle\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use LittleBigJoe\Bundle\CoreBundle\Entity\User;
use LittleBigJoe\Bundle\BackendBundle\Form\UserType;

/**
 * User controller.
 *
 * @Route("/users")
 */
class UserController extends Controller
{

    /**
     * Lists all User entities.
     *
     * @Route("/", name="littlebigjoe_backendbundle_users")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $dql = "SELECT u FROM LittleBigJoeCoreBundle:User u";
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
     * Creates a new User entity.
     *
     * @Route("/", name="littlebigjoe_backendbundle_users_create")
     * @Method("POST")
     * @Template("LittleBigJoeBackendBundle:User:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new User();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();            
            $em->persist($entity);
                        
            if ($entity->getPhoto() != null) {
                $evm = $em->getEventManager();
                $uploadableManager = $this->get('stof_doctrine_extensions.uploadable.manager');
                $evm->removeEventListener(array('postFlush'), $uploadableManager->getUploadableListener());
                $uploadableManager->markEntityToUpload($entity, $entity->getLogo());
            }

            $em->flush();
            
            // Create user in MangoPay
            $api = $this->container->get('little_big_joe_mango_pay.api');
            $mangopayUser = $api->createUser($entity->getEmail(), $entity->getFirstname(), $entity->getLastname(), $entity->getIpAddress(), $entity->getBirthday()->getTimestamp(), $entity->getNationality(), $entity->getPersonType(), $entity->getId());
            if (!empty($mangopayUser))
            {
	            	if (!empty($mangopayUser->ID))
	            	{
	            			$entity->setMangopayUserId($mangopayUser->ID);
	            	}
	            	if (!empty($mangopayUser->CreationDate))
	            	{
		            		$entity->setMangopayCreatedAt(new \DateTime('@'.$mangopayUser->CreationDate));
		            		$entity->setMangopayUpdatedAt(new \DateTime('@'.$mangopayUser->CreationDate));
	            	}
	            	if (!empty($mangopayUser->UpdateDate))
	            	{
	            			$entity->setMangopayUpdatedAt(new \DateTime('@'.$mangopayUser->UpdateDate));
	            	}
	            	$em->persist($entity);
	            	$em->flush();
            }
            
            // Send welcome email
            $email = \Swift_Message::newInstance()
					            ->setContentType('text/html')
					            ->setSubject($this->container->get('translator')->trans('Welcome to Little Big Joe'))
					            ->setFrom($this->container->getParameter('default_email_address'))
					            ->setTo(array($entity->getEmail() => $entity))
					            ->setBody(
					            		$this->container->get('templating')->render('LittleBigJoeFrontendBundle:Email:welcome.html.twig', array(
					            				'user' => $entity,
					            				'plainPassword' => '',
					            				'url' => $this->container->get('request')->getSchemeAndHttpHost()
					            		), 'text/html')
					            );
            $this->container->get('mailer')->send($email);

            return $this->redirect($this->generateUrl('littlebigjoe_backendbundle_users_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a form to create a User entity.
     *
     * @param User $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(User $entity)
    {
        $form = $this->createForm('littlebigjoe_bundle_backendbundle_user', $entity, array(
            'action' => $this->generateUrl('littlebigjoe_backendbundle_users_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new User entity.
     *
     * @Route("/new", name="littlebigjoe_backendbundle_users_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new User();
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a User entity.
     *
     * @Route("/{id}", name="littlebigjoe_backendbundle_users_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LittleBigJoeCoreBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     * @Route("/{id}/edit", name="littlebigjoe_backendbundle_users_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LittleBigJoeCoreBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
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
     * Creates a form to edit a User entity.
     *
     * @param User $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(User $entity)
    {
        $form = $this->createForm('littlebigjoe_bundle_backendbundle_user', $entity, array(
            'action' => $this->generateUrl('littlebigjoe_backendbundle_users_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing User entity.
     *
     * @Route("/{id}", name="littlebigjoe_backendbundle_users_update")
     * @Method("PUT")
     * @Template("LittleBigJoeBackendBundle:User:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LittleBigJoeCoreBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            if ($entity->getPhoto() != null) {
                $evm = $em->getEventManager();
                $uploadableManager = $this->get('stof_doctrine_extensions.uploadable.manager');
                $evm->removeEventListener(array('postFlush'), $uploadableManager->getUploadableListener());
                $uploadableManager->markEntityToUpload($entity, $entity->getPhoto());
            }

            $em->flush();

            return $this->redirect($this->generateUrl('littlebigjoe_backendbundle_users_edit', array('id' => $id)));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a User entity.
     *
     * @Route("/{id}", name="littlebigjoe_backendbundle_users_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LittleBigJoeCoreBundle:User')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find User entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('littlebigjoe_backendbundle_users'));
    }

    /**
     * Creates a form to delete a User entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('littlebigjoe_backendbundle_users_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }
}
