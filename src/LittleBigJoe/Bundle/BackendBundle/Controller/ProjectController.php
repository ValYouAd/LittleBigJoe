<?php

namespace LittleBigJoe\Bundle\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use LittleBigJoe\Bundle\CoreBundle\Entity\Project;
use LittleBigJoe\Bundle\BackendBundle\Form\ProjectType;
use LittleBigJoe\Bundle\CoreBundle\Entity\Withdrawal;
use LittleBigJoe\Bundle\BackendBundle\Form\WithdrawalType;

/**
 * Project controller.
 *
 * @Route("/projects")
 */
class ProjectController extends Controller
{
    /**
     * Lists all Project entities.
     *
     * @Route("/", name="littlebigjoe_backendbundle_projects")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $dql = "SELECT p FROM LittleBigJoeCoreBundle:Project p";
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
     * Lists all Project entities that are in "funding phase".
     *
     * @Route("/funding-phase-projects", name="littlebigjoe_backendbundle_projects_funding_phase")
     * @Method("GET")
     * @Template("LittleBigJoeBackendBundle:Project:funding_phase.html.twig")
     */
    public function fundingPhaseProjectsAction()
    {
	    	$em = $this->getDoctrine()->getManager();
	    
	    	$dql = "SELECT p FROM LittleBigJoeCoreBundle:Project p WHERE p.status = 2";
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
     * Lists all Project entities that are in "engagement phase".
     *
     * @Route("/engagement-phase-projects", name="littlebigjoe_backendbundle_projects_engagement_phase")
     * @Method("GET")
     * @Template("LittleBigJoeBackendBundle:Project:engagement_phase.html.twig")
     */
    public function engagementPhaseProjectsAction()
    {
	    	$em = $this->getDoctrine()->getManager();
	    	 
	    	$dql = "SELECT p FROM LittleBigJoeCoreBundle:Project p WHERE p.status = 1";
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
     * Lists all Project entities that are in ended.
     *
     * @Route("/ended-projects", name="littlebigjoe_backendbundle_projects_ended")
     * @Method("GET")
     * @Template("LittleBigJoeBackendBundle:Project:ended.html.twig")
     */
    public function endedProjectsAction()
    {
	    	$em = $this->getDoctrine()->getManager();
	    	 
	    	$dql = "SELECT p FROM LittleBigJoeCoreBundle:Project p WHERE p.endedAt IS NOT NULL";
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
     * Creates a new Project entity.
     *
     * @Route("/", name="littlebigjoe_backendbundle_projects_create")
     * @Method("POST")
     * @Template("LittleBigJoeBackendBundle:Project:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Project();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);

            if ($entity->getPhoto() != null) {
	            	$evm = $em->getEventManager();
	            	$uploadableManager = $this->container->get('stof_doctrine_extensions.uploadable.manager');
	            	$uploadableListener = $uploadableManager->getUploadableListener();
	            	$uploadableListener->setDefaultPath('uploads/projects/'.$entity->getId());
	            	$evm->removeEventListener(array('postFlush'), $uploadableListener);
	            	$uploadableManager->markEntityToUpload($entity, $entity->getPhoto());
            }

            $em->flush();
            
            // Create project in MangoPay
            $api = $this->container->get('little_big_joe_mango_pay.api');
            $mangopayProject = $api->createProject($entity->getUser()->getMangopayUserId(), array($entity->getUser()->getMangopayUserId()), $entity->getId(), $entity->getName(), $entity->getPitch(), $entity->getAmountRequired(), $entity->getEndingAt()->getTimestamp());
            if (!empty($mangopayProject))
            {
	            	if (!empty($mangopayProject->ID))
	            	{
	            			$entity->setMangopayWalletId($mangopayProject->ID);
	            	}
	            	if (!empty($mangopayProject->CreationDate))
	            	{
		            		$entity->setMangopayCreatedAt(new \DateTime('@'.$mangopayProject->CreationDate));
		            		$entity->setMangopayUpdatedAt(new \DateTime('@'.$mangopayProject->CreationDate));
	            	}
	            	if (!empty($mangopayProject->UpdateDate))
	            	{
	            			$entity->setMangopayUpdatedAt(new \DateTime('@'.$mangopayProject->UpdateDate));
	            	}
	            	$em->persist($entity);
	            	$em->flush();
            }

            return $this->redirect($this->generateUrl('littlebigjoe_backendbundle_projects_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Project entity.
     *
     * @param Project $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Project $entity)
    {
        $form = $this->createForm(new ProjectType(), $entity, array(
            'action' => $this->generateUrl('littlebigjoe_backendbundle_projects_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Project entity.
     *
     * @Route("/new", name="littlebigjoe_backendbundle_projects_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Project();
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a Project entity.
     *
     * @Route("/{id}", name="littlebigjoe_backendbundle_projects_show")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function showAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $api = $this->container->get('little_big_joe_mango_pay.api');
        $paginator = $this->get('knp_paginator');
        
        $entity = $em->getRepository('LittleBigJoeCoreBundle:Project')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Project entity.');
        }
        
        // Total of transfers for the project
        $totalWithdrawalAmount = $em->getRepository('LittleBigJoeCoreBundle:Withdrawal')->count($entity->getId());
				if (empty($totalWithdrawalAmount))
				{
						$totalWithdrawalAmount = 0;
				}
				
        // Operations for the project
        $operations = $api->fetchOperations($entity->getMangopayWalletId());
        $operations_pagination = array();
        if (!empty($operations))
        {
	        	foreach ($operations as $key => $operation)
	        	{	
	        			$user = $em->getRepository("LittleBigJoeCoreBundle:User")->findOneByMangopayUserId($operation->UserID);
	        			if (empty($user))
	        			{
	        					$user = 'N/A';
								}
								$operations[$key]->User = $user;  
	        	}

		        $operations_pagination = $paginator->paginate(
		        		$operations,
		        		$this->get('request')->query->get('page1', 1),
		        		5,
		        		array(
		        				'pageParameterName' => 'page1',
		        				'sortFieldParameterName' => 'sort1',
		        				'sortDirectionParameterName' => 'direction1'
		        		)
		        );  
        }
        
        // Contributors for the project
        $contributors = $api->listUsers($entity->getMangopayWalletId());
        $contributors_pagination = array();
        if (!empty($contributors))
        {
	        	foreach ($contributors as $key => $contributor)
	        	{
		        		$user = $em->getRepository("LittleBigJoeCoreBundle:User")->findOneByMangopayUserId($contributor->ID);
		        		if (empty($user))
		        		{
		        				$user = 'N/A';
		        		}
		        		$contributors[$key]->User = $user;
	        	}
	        	
	        	$contributors_pagination = $paginator->paginate(
		        		$contributors,
		        		$this->get('request')->query->get('page2', 1),
		        		5,
		        		array(
		        				'pageParameterName' => 'page2',
		        				'sortFieldParameterName' => 'sort2',
		        				'sortDirectionParameterName' => 'direction2'
		        		)
		        );  
        }

        // Withdrawal form
        $withdrawal = new Withdrawal();
        $withdrawal->setProject($entity);
        $withdrawal->setUser($entity->getUser());
        $withdrawForm = $this->createForm(new WithdrawalType(), $withdrawal);
        $withdrawForm->handleRequest($request);
        
        if ($withdrawForm->isValid()) {
	        	$em->persist($withdrawal);
	        	$em->flush();
              	
	        	// Create withdrawal in MangoPay
	        	$api = $this->container->get('little_big_joe_mango_pay.api');
	        	$mangopayWithdrawal = $api->createWithdrawal($withdrawal->getUser()->getMangopayUserId(), $withdrawal->getProject()->getMangopayWalletId(), $withdrawal->getBeneficiary()->getMangopayBeneficiaryId(), $withdrawal->getMangopayAmount()*100, $withdrawal->getMangopayClientFeeAmount(), $withdrawal->getId());
	        	if (!empty($mangopayWithdrawal))
	        	{
		        		if (!empty($mangopayWithdrawal->ID))
		        		{
		        				$withdrawal->setMangopayWithdrawalId($mangopayWithdrawal->ID);
		        		}
		        		if (isset($mangopayWithdrawal->Error))
		        		{
		        				$withdrawal->setMangopayError($mangopayWithdrawal->Error);
		        		}
		        		if (!empty($mangopayWithdrawal->CreationDate))
		        		{
			        			$withdrawal->setMangopayCreatedAt(new \DateTime('@'.$mangopayWithdrawal->CreationDate));
			        			$withdrawal->setMangopayUpdatedAt(new \DateTime('@'.$mangopayWithdrawal->CreationDate));
		        		}
		        		if (!empty($mangopayWithdrawal->UpdateDate))
		        		{
		        				$withdrawal->setMangopayUpdatedAt(new \DateTime('@'.$mangopayWithdrawal->UpdateDate));
		        		}
		        		
		        		// We force booleans to true, because of MangoPay Withdrawals validation
		        		$withdrawal->setMangopayIsSucceeded(true);
		        		$withdrawal->setMangopayIsCompleted(true);
		        		
		        		$em->persist($withdrawal);
		        		$em->flush();
	        	}
        }
        
        // Withdrawals for the project
        $withdrawals_dql = "SELECT w FROM LittleBigJoeCoreBundle:Withdrawal w WHERE w.project = :project ORDER BY w.createdAt DESC";
        $withdrawals_query = $em->createQuery($withdrawals_dql)->setParameter('project', $entity);
        
        $withdrawals_pagination = $paginator->paginate(
        		$withdrawals_query,
        		$this->get('request')->query->get('page1', 1),
        		5,
        		array(
        				'pageParameterName' => 'page3',
        				'sortFieldParameterName' => 'sort3',
        				'sortDirectionParameterName' => 'direction3'
        		)
        );
                
        return array(
            'entity' => $entity,
        		'totalWithdrawalAmount' => $totalWithdrawalAmount,
        		'withdraw_form' => $withdrawForm->createView(),
        		'operations_pagination' => $operations_pagination,
        		'contributors_pagination' => $contributors_pagination,
        		'withdrawals_pagination' => $withdrawals_pagination
        );
    }
    
    /**
     * Displays a form to edit an existing Project entity.
     *
     * @Route("/{id}/edit", name="littlebigjoe_backendbundle_projects_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LittleBigJoeCoreBundle:Project')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Project entity.');
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
     * Creates a form to edit a Project entity.
     *
     * @param Project $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Project $entity)
    {
        $form = $this->createForm(new ProjectType(), $entity, array(
            'action' => $this->generateUrl('littlebigjoe_backendbundle_projects_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Project entity.
     *
     * @Route("/{id}", name="littlebigjoe_backendbundle_projects_update")
     * @Method("PUT")
     * @Template("LittleBigJoeBackendBundle:Project:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LittleBigJoeCoreBundle:Project')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Project entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
        		if ($entity->getPhoto() != null) {
	            	$evm = $em->getEventManager();
	            	$uploadableManager = $this->container->get('stof_doctrine_extensions.uploadable.manager');
	            	$uploadableListener = $uploadableManager->getUploadableListener();
	            	$uploadableListener->setDefaultPath('uploads/projects/'.$entity->getId());
	            	$evm->removeEventListener(array('postFlush'), $uploadableListener);
	            	$uploadableManager->markEntityToUpload($entity, $entity->getPhoto());
            }

            $em->flush();

            return $this->redirect($this->generateUrl('littlebigjoe_backendbundle_projects_edit', array('id' => $id)));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Project entity.
     *
     * @Route("/{id}", name="littlebigjoe_backendbundle_projects_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LittleBigJoeCoreBundle:Project')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Project entity.');
            }

            $entity->setDeletedAt(new \Datetime());
            $em->flush();
        }

        return $this->redirect($this->generateUrl('littlebigjoe_backendbundle_projects'));
    }

    /**
     * Creates a form to delete a Project entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('littlebigjoe_backendbundle_projects_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }
}
