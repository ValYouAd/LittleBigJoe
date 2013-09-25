<?php

namespace LittleBigJoe\Bundle\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;
use LittleBigJoe\Bundle\CoreBundle\Entity\Project;
use LittleBigJoe\Bundle\CoreBundle\Entity\ProjectReward;
use LittleBigJoe\Bundle\CoreBundle\Entity\Entry;
use LittleBigJoe\Bundle\FrontendBundle\Form\EntryType;
use LittleBigJoe\Bundle\CoreBundle\Entity\Comment;
use LittleBigJoe\Bundle\FrontendBundle\Form\CommentType;

class ProjectController extends Controller
{
    /**
     * Default handler for project
     *
     * @Route("/projects", name="littlebigjoe_frontendbundle_project")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $favoriteProjects = $em->getRepository('LittleBigJoeCoreBundle:Project')->findFavorite();
        $popularProjects = $em->getRepository('LittleBigJoeCoreBundle:Project')->findPopular(4, '-7 days');
        $recentlyUpdatedProjects = $em->getRepository('LittleBigJoeCoreBundle:Project')->findRecentlyUpdated();

        return array(
            'favoriteProjects' => $favoriteProjects,
            'popularProjects' => $popularProjects,
            'recentlyUpdatedProjects' => $recentlyUpdatedProjects
        );
    }

    /**
     * Latest projects
     *
     * @Route("/latest-projects", name="littlebigjoe_frontendbundle_project_latest_projects")
     * @Template("LittleBigJoeFrontendBundle:Project:list.html.twig")
     */
    public function latestProjectsAction()
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository('LittleBigJoeCoreBundle:Project')->findLatest(null);

        $paginator = $this->get('knp_paginator');
        $projects = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1),
            $this->container->getParameter('nb_elements_by_page')
        );

        return array(
            'title' => 'Latest projects',
            'projects' => $projects
        );
    }

    /**
     * Popular projects
     *
     * @Route("/popular-projects", name="littlebigjoe_frontendbundle_project_popular_projects")
     * @Template("LittleBigJoeFrontendBundle:Project:list.html.twig")
     */
    public function popularProjectsAction()
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository('LittleBigJoeCoreBundle:Project')->findPopular(null);

        $paginator = $this->get('knp_paginator');
        $projects = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1),
            $this->container->getParameter('nb_elements_by_page')
        );

        return array(
            'title' => 'Popular projects',
            'projects' => $projects
        );
    }

    /**
     * Popular week projects
     *
     * @Route("/popular-projects-this-week", name="littlebigjoe_frontendbundle_project_popular_week_projects")
     * @Template("LittleBigJoeFrontendBundle:Project:list.html.twig")
     */
    public function popularWeekProjectsAction()
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository('LittleBigJoeCoreBundle:Project')->findPopular(null, '-7 days');

        $paginator = $this->get('knp_paginator');
        $projects = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1),
            $this->container->getParameter('nb_elements_by_page')
        );

        return array(
            'title' => 'Popular week projects',
            'projects' => $projects
        );
    }

    /**
     * Funding projects
     *
     * @Route("/funding-projects", name="littlebigjoe_frontendbundle_project_funding_projects")
     * @Template("LittleBigJoeFrontendBundle:Project:list.html.twig")
     */
    public function fundingProjectsAction()
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository('LittleBigJoeCoreBundle:Project')->findFunding(null);

        $paginator = $this->get('knp_paginator');
        $projects = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1),
            $this->container->getParameter('nb_elements_by_page')
        );

        return array(
            'title' => 'Projects to fund',
            'projects' => $projects
        );
    }

    /**
     * Top funded projects
     *
     * @Route("/top-funded-projects", name="littlebigjoe_frontendbundle_project_top_funded_projects")
     * @Template("LittleBigJoeFrontendBundle:Project:list.html.twig")
     */
    public function topFundedProjectsAction()
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository('LittleBigJoeCoreBundle:Project')->findTopFunded(null);

        $paginator = $this->get('knp_paginator');
        $projects = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1),
            $this->container->getParameter('nb_elements_by_page')
        );

        return array(
            'title' => 'Top funded projects',
            'projects' => $projects
        );
    }

    /**
     * Almost ending projects
     *
     * @Route("/almost-ending-projects", name="littlebigjoe_frontendbundle_project_almost_ending_projects")
     * @Template("LittleBigJoeFrontendBundle:Project:list.html.twig")
     */
    public function almostEndingProjectsAction()
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository('LittleBigJoeCoreBundle:Project')->findAlmostEnding(null);

        $paginator = $this->get('knp_paginator');
        $projects = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1),
            $this->container->getParameter('nb_elements_by_page')
        );

        return array(
            'title' => 'Almost ending projects',
            'projects' => $projects
        );
    }

    /**
     * Favorite projects
     *
     * @Route("/favorite-projects", name="littlebigjoe_frontendbundle_project_favorite_projects")
     * @Template("LittleBigJoeFrontendBundle:Project:list.html.twig")
     */
    public function favoriteProjectsAction()
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository('LittleBigJoeCoreBundle:Project')->findFavorite(null);

        $paginator = $this->get('knp_paginator');
        $projects = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1),
            $this->container->getParameter('nb_elements_by_page')
        );

        return array(
            'title' => 'Favorite projects',
            'projects' => $projects
        );
    }

    /**
     * Recently updated projects
     *
     * @Route("/recently-updated-projects", name="littlebigjoe_frontendbundle_project_recently_updated_projects")
     * @Template("LittleBigJoeFrontendBundle:Project:list.html.twig")
     */
    public function recentlyUpdatedProjectsAction()
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository('LittleBigJoeCoreBundle:Project')->findRecentlyUpdated(null);

        $paginator = $this->get('knp_paginator');
        $projects = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1),
            $this->container->getParameter('nb_elements_by_page')
        );

        return array(
            'title' => 'Recently updated projects',
            'projects' => $projects
        );
    }

    /**
     * Create new project
     *
     * @Route("/launch-my-project", name="littlebigjoe_frontendbundle_project_create_project")
     * @Template("LittleBigJoeFrontendBundle:Project:create.html.twig")
     */
    public function createProjectAction()
    {
				$em = $this->getDoctrine()->getManager();
				
				$currentUser = $this->get('security.context')->getToken()->getUser();
				// If the current user is not logged, redirect him to login page
				if (!is_object($currentUser))
				{
						$this->get('session')->getFlashBag()->add(
								'notice',
								'You must be logged in to create a project'
						);
						
						return $this->redirect($this->generateUrl('fos_user_security_login'));
				}
								
        $project = new Project(); 
        // Set default data like creator and default language for project
        $project->setUser($currentUser);
        $project->setLanguage($currentUser->getDefaultLanguage());
        $project->setAmountCount(0);
        $project->setLikesCount(0);
        $project->setCreatedAt(new \DateTime());
        $project->setStatus('1');
        
        $projectReward = new ProjectReward();       
        $projectReward->setProject($project);
        $project->getRewards()->add($projectReward);
        
        // Create form flow
		    $flow = $this->get('littlebigjoefrontend.flow.project.createProject');
		    $flow->bind($project);
		
		    $form = $flow->createForm();
		    
		    if ($flow->isValid($form)) 
		    {
		    		// Handle file upload in first step
				    $photo = $this->_fixUploadFile($project->getPhoto());
		        $flow->saveCurrentStepData($form);
		        
		        // If we're not on the final step
		        if ($flow->nextStep()) 
		        {
		            // Create form for next step
		            $form = $flow->createForm();
		        } 
		        else 
		        {		    
		        		// Set default project for associated rewards
		        		foreach ($project->getRewards() as $projectReward)
		        		{	
		        				$projectReward->setProject($project);
		        		}
		        		
		        		// Persist form data
		            $em->persist($project);		
		            $em->flush();
		            
		            // Move tmp file from server, to project directory
		            $matches = array();
		            preg_match_all('/\b(?:(?:https?):\/\/'.$this->getRequest()->getHost().')[-A-Z0-9+&@#\/%=~_|$?!:,.]*[A-Z0-9+&]/i', $project->getDescription(), $matches, PREG_PATTERN_ORDER);
		            foreach ($matches[0] as $key => $match)
		            {
			            	if (@fopen($match, 'r'))
			            	{
				            		// Create project directory if it doesn't exist
				            		if (!is_dir(__DIR__.'/../../../../../web/uploads/projects/'.$project->getId()))
				            		{
				            			mkdir(__DIR__.'/../../../../../web/uploads/projects/'.$project->getId(), 0755);
				            		}
				            
				            		// Move file
				            		$filePath = preg_replace('/\b(?:(?:https?):\/\/'.$this->getRequest()->getHost().')/i', '', $match);
				            		copy(__DIR__.'/../../../../../web'.$filePath, __DIR__.'/../../../../../web/uploads/projects/'.$project->getId().'/'.basename($filePath));
				            
				            		// Update description field
				            		$description = preg_replace('#'.$filePath.'#', '/uploads/projects/'.$project->getId().'/'.basename($filePath), $project->getDescription());
				            		$project->setDescription($description);
			            	}
		            }
		            
		            // Retrieve the uploaded photo, and associate it with project
		            if ($this->getRequest()->getSession()->get('tmpUploadedFilePath') != null)
		            {
			            	$fileInfo = new UploadedFile(
			            			$this->getRequest()->getSession()->get('tmpUploadedFilePath'),
			            			$this->getRequest()->getSession()->get('tmpUploadedFile'),
			            			MimeTypeGuesser::getInstance()->guess($this->getRequest()->getSession()->get('tmpUploadedFilePath')),
			            			filesize($this->getRequest()->getSession()->get('tmpUploadedFilePath'))
			            	);
			            	$project->setPhoto($fileInfo);
			            
			            	$evm = $em->getEventManager();
			            	$uploadableManager = $this->container->get('stof_doctrine_extensions.uploadable.manager');
			            	$uploadableListener = $uploadableManager->getUploadableListener();
			            	$uploadableListener->setDefaultPath('uploads/projects/'.$project->getId());
			            	$evm->removeEventListener(array('postFlush'), $uploadableListener);
			            	$uploadableManager->markEntityToUpload($project, $project->getPhoto());
		            }
		            
		            // Persist form data and redirect user
		            $em->persist($project);
		            $em->flush();
		            
								// Delete session data
		            $this->getRequest()->getSession()->remove('tmpUploadedFile');
		            $this->getRequest()->getSession()->remove('tmpUploadedFilePath');
		            
		            // Reset flow data
		            $flow->reset();
		            return $this->redirect($this->generateUrl('littlebigjoe_frontendbundle_home'));
		        }
		    }
		    
		    return $this->render('LittleBigJoeFrontendBundle:Project:new.html.twig', array(
		        'form' => $form->createView(),
		        'flow' => $flow,
		    ));
    }
    
    /**
     * Allows to save project logo in project creation during step 1
     * 
     * @param UploadedFile $file
     * @return string
     */
    public function _fixUploadFile($file) 
    {    
    		$currentUser = $this->get('security.context')->getToken()->getUser();
    	
	    	if (!empty($file) && $file instanceof UploadedFile) 
	    	{
	    			// Move uploaded file to tmp directory, and save path in session
		    		$tmpFile = $file->move(__DIR__.'/../../../../../web/uploads/tmp/user/'.preg_replace('/[^a-z0-9_\-]/i', '_', $currentUser->getEmail()).'/', sha1($file->getClientOriginalName().uniqid(mt_rand(), true)));
		    		if (!empty($tmpFile))
		    		{
			    			$tmpFilePath = $tmpFile->getPath().$tmpFile->getFilename();
			    			$this->getRequest()->getSession()->set('tmpUploadedFile', $tmpFile->getFilename());
			    			$this->getRequest()->getSession()->set('tmpUploadedFileRelativePath', '/uploads/tmp/user/'.preg_replace('/[^a-z0-9_\-]/i', '_', $currentUser->getEmail()).'/');
			    			$this->getRequest()->getSession()->set('tmpUploadedFilePath', $tmpFilePath);
		    		}
	    	}
	    	
	    	return $this->getRequest()->getSession()->get('tmpUploadedFilePath');    
    }
    
    /**
     * Specific project
     *
     * @Route("/project/{slug}", name="littlebigjoe_frontendbundle_project_show")
     * @Template()
     */
    public function showAction(Request $request, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        $currentUser = $this->get('security.context')->getToken()->getUser();
        $entity = $em->getRepository('LittleBigJoeCoreBundle:Project')->findBySlugI18n($slug);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Project entity.');
        }
        
        // Create the entry form
        $entry = new Entry();
        $entry->setProject($entity);        
        $entryForm = $this->createForm(new EntryType(), $entry);
	      
	      // Create the comment form
	      $comment = new Comment();
	      $comment->setProject($entity);
	      $comment->setIsVisible(true);
	      $commentForm = $this->createForm(new CommentType(), $comment);
											        
        // Create the funding form
        $fundingForm = $this->createFormBuilder()
										        ->setAction($this->generateUrl('littlebigjoe_frontendbundle_payment_project'))
										        ->setMethod('POST')
										        ->add('projectId', 'hidden', array(
				        								'data' => $entity->getId()
				        						))
				        						->add('submit', 'submit', array(
				        								'label' => 'Fund this project',
										        		'attr' => array(
				        										'class' => 'btn btn-success'
				        								)
										        ))
										        ->getForm();
        
        return array(
            'entity' => $entity,
        		'entry_form' => $entryForm->createView(),
        		'comment_form' => $commentForm->createView(),
        		'funding_form' => $fundingForm->createView(),
            'current_date' => new \Datetime()
        );
    }
    
    /**
     * Project preview
     *
     * @Route("/project/preview", name="littlebigjoe_frontendbundle_project_preview")
     * @Template("LittleBigJoeFrontendBundle:Project:preview.html.twig")
     */
    public function previewAction($entity)
    {    	
	    	$photo = '';
	    	
	    	// Retrieve the uploaded photo, and associate it with project
	    	if ($this->getRequest()->getSession()->get('tmpUploadedFile') != null && $this->getRequest()->getSession()->get('tmpUploadedFileRelativePath') != null)
	    	{
	    			$photo = $this->getRequest()->getSession()->get('tmpUploadedFileRelativePath').
	    							 $this->getRequest()->getSession()->get('tmpUploadedFile');
	    	}
	    	
	    	return array(
	    			'entity' => $entity,
	    			'photo' => $photo,
	    			'current_date' => new \Datetime()
	    	);
    }
}
