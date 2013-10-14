<?php

namespace LittleBigJoe\Bundle\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Mangopay controller.
 *
 * @Route("/mangopay")
 */
class MangopayController extends Controller
{

    /**
     * Lists all MangoPay infos
     *
     * @Route("/", name="littlebigjoe_backendbundle_mangopay")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
				$paginator = $this->get('knp_paginator');
				
        // Funding phase projects
        $fundingSearch = $request->query->get('funding_where', '');
        $funding_phase_dql = 'SELECT p
        											FROM LittleBigJoeCoreBundle:Project p 
        											WHERE p.status = 2 '.((!empty($fundingSearch)) ? "AND p.name LIKE '%".$fundingSearch."%'" : '').' 
        											ORDER BY p.createdAt DESC';        
        $funding_phase_query = $em->createQuery($funding_phase_dql);
        
        $funding_phase_pagination = $paginator->paginate(
            $funding_phase_query,
            $this->get('request')->query->get('page1', 1),
            $this->container->getParameter('nb_elements_by_page'),
        		array(
        				'pageParameterName' => 'page1',
        				'sortFieldParameterName' => 'sort1', 
        				'sortDirectionParameterName' => 'direction1'
        		)
        );
        
        // Last contributions
        $lastSearch = $request->query->get('last_where', '');
        $last_contributions_dql = 'SELECT pc	
        													FROM LittleBigJoeCoreBundle:ProjectContribution pc
        													LEFT JOIN pc.project p
        													WHERE pc.mangopayIsSucceeded = 1 AND pc.mangopayIsCompleted = 1
        													'.((!empty($lastSearch)) ? "AND p.name LIKE '%".$lastSearch."%'" : '').' 
        													ORDER BY pc.createdAt DESC'; 
        $last_contributions_query = $em->createQuery($last_contributions_dql);
        
        $last_contributions_pagination = $paginator->paginate(
        		$last_contributions_query,
        		$this->get('request')->query->get('page2', 1),
        		$this->container->getParameter('nb_elements_by_page'),
        		array(
        				'pageParameterName' => 'page2',
        				'sortFieldParameterName' => 'sort2', 
        				'sortDirectionParameterName' => 'direction2'
        		)
        );
        
        // Ended projects
        $endedSearch = $request->query->get('ended_where', '');
        $ended_dql = 'SELECT p
        							FROM LittleBigJoeCoreBundle:Project p 
        							WHERE p.endedAt IS NOT NULL '.((!empty($endedSearch)) ? "AND p.name LIKE '%".$endedSearch."%'" : '').' 
        							ORDER BY p.createdAt DESC';
        $ended_query = $em->createQuery($ended_dql);
        
        $ended_pagination = $paginator->paginate(
        		$ended_query,
        		$this->get('request')->query->get('page3', 1),
        		$this->container->getParameter('nb_elements_by_page'),
        		array(
        				'pageParameterName' => 'page3',
        				'sortFieldParameterName' => 'sort3',
        				'sortDirectionParameterName' => 'direction3'
        		)
        );

        return array(
            'funding_phase_pagination' => $funding_phase_pagination,
        		'last_contributions_pagination' => $last_contributions_pagination,
        		'ended_pagination' => $ended_pagination,
        );
    }
}
