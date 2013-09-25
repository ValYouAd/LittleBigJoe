<?php

namespace LittleBigJoe\Bundle\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use LittleBigJoe\Bundle\CoreBundle\Entity\ProjectLike;
use LittleBigJoe\Bundle\CoreBundle\Entity\Entry;
use LittleBigJoe\Bundle\CoreBundle\Entity\Comment;

/**
 * Ajax controller.
 *
 * @Route("/ajax", options={ "i18n": false })
 */
class AjaxController extends Controller
{
    /**
     * Like a project
     *
     * @Route("/like-project", name="littlebigjoe_frontendbundle_ajax_like_project")
     * @Method("POST")
     * @Template()
     */
    public function likeProjectAction()
    {    		
    		$em = $this->getDoctrine()->getManager();
    		$projectId = (int)$this->get('request')->request->get('projectId');

    		$currentUser = $this->get('security.context')->getToken()->getUser();
				// If the current user is not logged, redirect him to login page
				if (!is_object($currentUser))
				{
						$this->get('session')->getFlashBag()->add(
								'notice',
								'You must be logged in to like this project'
						);
						
						return new JsonResponse(array('status' => 'KO USER'));
				}
    		
    		// If it's not a correct project id
    		if (empty($projectId))
    		{
						return new JsonResponse(array('status' => 'KO ID'));
    		}
    		
    		$project = $em->getRepository('LittleBigJoeCoreBundle:Project')->find($projectId);
    		
    		// If the project doesn't exist
    		if (empty($project))
    		{
    				return new JsonResponse(array('status' => 'KO PROJECT'));
    		}    		
    		
    		$projectLikeExists = $em->getRepository('LittleBigJoeCoreBundle:ProjectLike')->findOneBy(array(
    				'project' => $project->getId(), 
    				'user' => $currentUser->getId()
    		));
    		
    		// If user has already liked the project
    		if (!empty($projectLikeExists) && $projectLikeExists instanceof ProjectLike)
    		{
    				return new JsonResponse(array('status' => 'KO VOTE'));
    		}
    		
    		$projectLike = new ProjectLike();
    		$projectLike->setProject($project);
    		$projectLike->setUser($currentUser);
    		
    		// Save like in DB
    		$em->persist($projectLike);
    		$em->flush();
    		
    		// Make sure no code is executed after it
    		return new JsonResponse(array('status' => 'OK'));
    		exit;
    }
    
    /**
     * Create an entry for a project
     *
     * @Route("/entry-project", name="littlebigjoe_frontendbundle_ajax_entry_project")
     * @Method("POST")
     * @Template()
     */
    public function entryProjectAction()
    {
	    	$em = $this->getDoctrine()->getManager();
	    	$formData = $this->get('request')->request->get('entry');

	    	// If there's no title/content/public
	    	if (empty($formData) || empty($formData['title']) || empty($formData['content']) || $formData['isPublic'] == null)
	    	{
	    			return new JsonResponse(array('status' => 'KO FIELD'));
	    	}

	    	$currentUser = $this->get('security.context')->getToken()->getUser();
	    	// If the current user is not logged, redirect him to login page
	    	if (!is_object($currentUser))
	    	{
		    		$this->get('session')->getFlashBag()->add(
		    				'notice',
		    				'You must be logged in to post an entry for this project'
		    		);
		    		 
		    		return new JsonResponse(array('status' => 'KO USER'));
	    	}
	    	
	    	// If it's not a correct project id
	    	if (empty($formData) || empty($formData['project']))
	    	{
	    			return new JsonResponse(array('status' => 'KO ID'));
	    	}
	    	 
	    	$project = $em->getRepository('LittleBigJoeCoreBundle:Project')->find($formData['project']);

	    	// If the project doesn't exist or if current user is not the project owner
	    	if (empty($project) || ($currentUser->getId() != $project->getUser()->getId()))
	    	{
	    			return new JsonResponse(array('status' => 'KO PROJECT'));
	    	}
	    		
	    	$entry = new Entry();
	    	$entry->setProject($project);
	    	$entry->setTitle($formData['title']);
	    	$entry->setContent($formData['content']);
	    	$entry->setIsPublic(($formData['isPublic'] == '1'));
	    		    		    	
	    	// Save entry in DB
	    	$em->persist($entry);
	    	$em->flush();
	    
	    	$entryJson = array(
	    			'title' => $entry->getTitle(),
	    			'is_public' => $entry->getIsPublic(),
	    			'created_at' => $entry->getCreatedAt()->format('d/m/Y h:i'),
	    			'content' => $entry->getContent()
	    	);
	    
	    	// Make sure no code is executed after it
	    	return new JsonResponse(array('status' => 'OK', 'entry' => $entryJson));
	    	exit;
    }
    
    /**
     * Comment a project
     *
     * @Route("/comment-project", name="littlebigjoe_frontendbundle_ajax_comment_project")
     * @Method("POST")
     * @Template()
     */
    public function commentProjectAction()
    {
    		$em = $this->getDoctrine()->getManager();
	    	$formData = $this->get('request')->request->get('comment');
	    
    		// If there's no content
	    	if (empty($formData) || empty($formData['content']))
	    	{
	    			return new JsonResponse(array('status' => 'KO FIELD'));
	    	}
	    		    	
	    	$currentUser = $this->get('security.context')->getToken()->getUser();
	    	// If the current user is not logged, redirect him to login page
	    	if (!is_object($currentUser))
	    	{
	    		$this->get('session')->getFlashBag()->add(
	    				'notice',
	    				'You must be logged in to comment this project'
	    		);
	    
	    		return new JsonResponse(array('status' => 'KO USER'));
	    	}
	    
	    	// If it's not a correct project id
	    	if (empty($formData) || empty($formData['project']))
	    	{
	    			return new JsonResponse(array('status' => 'KO ID'));
	    	}
	    
	    	$project = $em->getRepository('LittleBigJoeCoreBundle:Project')->find($formData['project']);
	    
	    	// If the project doesn't exist or if current user is the project owner
	    	if (empty($project) || ($currentUser->getId() == $project->getUser()->getId()))
	    	{
	    			return new JsonResponse(array('status' => 'KO PROJECT'));
	    	}	    	
	    		    
	    	$comment = new Comment();
	    	$comment->setProject($project);
	    	$comment->setUser($currentUser);
	    	$comment->setIsVisible(true);
	    	$comment->setContent($formData['content']);
	    	
	    	// Save comment in DB
	    	$em->persist($comment);
	    	$em->flush();
	    	
	    	$commentJson = array(
	    			'user_name' => (string)$currentUser,
	    			'user_id' => $currentUser->getId(),
	    			'created_at' => $comment->getCreatedAt()->format('d/m/Y h:i'),
	    			'content' => $comment->getContent()
	   		);
	    	
	    	// Make sure no code is executed after it
	    	return new JsonResponse(array('status' => 'OK', 'comment' => $commentJson));
	    	exit;
    }
    
    /**
     * Upload new project file
     *
     * @Route("/upload-file", name="littlebigjoe_frontendbundle_ajax_upload_file")
     * @Method("POST")
     * @Template()
     */
    public function uploadFileAction()
    {
    		$em = $this->getDoctrine()->getManager();
    		$file = $_FILES['fileName'];

    		$currentUser = $this->get('security.context')->getToken()->getUser();
				// If the current user is not logged, redirect him to login page
				if (!is_object($currentUser))
				{
						$this->get('session')->getFlashBag()->add(
								'notice',
								'You must be logged in to create a project'
						);
						
						return new JsonResponse(array('status' => 'KO USER'));
				}
    		
    		// If it's not a correct file object
    		if (empty($file) && !is_array($file))
    		{
						return new JsonResponse(array('status' => 'KO FILE'));
    		}

    		// If it's not an allowed MIME type
    		if (!in_array($file['type'], $this->container->getParameter('allowed_mime_types')))
    		{
    				return new JsonResponse(array('status' => 'KO TYPE'));
    		}
    		
    		// If file size is too big
    		if ($file['size'] > $this->container->getParameter('max_file_size'))
    		{
    				return new JsonResponse(array('status' => 'KO SIZE'));
    		}
    		    		
    		// Move file to tmp folder
    		$tmpName = sha1($file['name'].uniqid(mt_rand(), true));
    		$absolutePath = __DIR__.'/../../../../../web/uploads/tmp/user/'.preg_replace('/[^a-z0-9_\-]/i', '_', $currentUser->getEmail()).'/';
    		$relativePath = $this->getRequest()->getSchemeAndHttpHost().'/uploads/tmp/user/'.preg_replace('/[^a-z0-9_\-]/i', '_', $currentUser->getEmail()).'/';
    		@move_uploaded_file($file['tmp_name'], $absolutePath.$tmpName);
    		
    		// Generate the code that will be added to CKEditor
    		$html = '<a href="'.$relativePath.$tmpName.'">'.$file['name'].'</a>';
    		
	    	// Make sure no code is executed after it
	    	return new JsonResponse(array('status' => 'OK', 'html' => $html));	    	
	    	exit;
    }
    
    /**
     * Upload new audio project file
     *
     * @Route("/upload-audio-file", name="littlebigjoe_frontendbundle_ajax_upload_audio_file")
     * @Method("POST")
     * @Template()
     */
    public function uploadAudioFileAction()
    {
    		$em = $this->getDoctrine()->getManager();
    		$file = $_FILES['audioFileName'];

    		$currentUser = $this->get('security.context')->getToken()->getUser();
				// If the current user is not logged, redirect him to login page
				if (!is_object($currentUser))
				{
						$this->get('session')->getFlashBag()->add(
								'notice',
								'You must be logged in to create a project'
						);
						
						return new JsonResponse(array('status' => 'KO USER'));
				}
    		
    		// If it's not a correct file object
    		if (empty($file) && !is_array($file))
    		{
						return new JsonResponse(array('status' => 'KO FILE'));
    		}

    		// If it's not an allowed MIME type
    		if (!in_array($file['type'], $this->container->getParameter('allowed_audio_mime_types')))
    		{
    				return new JsonResponse(array('status' => 'KO TYPE'));
    		} 		

    		// If file size is too big
    		if ($file['size'] > $this->container->getParameter('max_audio_file_size'))
    		{
    				return new JsonResponse(array('status' => 'KO SIZE'));
    		}
    		
    		// Move file to tmp folder
    		$tmpName = sha1($file['name'].uniqid(mt_rand(), true));
    		$absolutePath = __DIR__.'/../../../../../web/uploads/tmp/user/'.preg_replace('/[^a-z0-9_\-]/i', '_', $currentUser->getEmail()).'/';
    		$relativePath = $this->getRequest()->getSchemeAndHttpHost().'/uploads/tmp/user/'.preg_replace('/[^a-z0-9_\-]/i', '_', $currentUser->getEmail()).'/';
    		@move_uploaded_file($file['tmp_name'], $absolutePath.$tmpName);
    		
    		// Generate the code that will be added to CKEditor
    		$html = '<object type="application/x-shockwave-flash" data="/bundles/littlebigjoefrontend/flash/dewplayer/dewplayer-mini.swf" width="160" height="20" id="player_'.$tmpName.'" name="player_'.$tmpName.'"><param name="wmode" value="transparent" /><param name="movie" value="/bundles/littlebigjoefrontend/flash/dewplayer/dewplayer-mini.swf" /><param name="flashvars" value="showtime=1&amp;mp3='.$relativePath.$tmpName.'" /></object>';
    		
	    	// Make sure no code is executed after it
	    	return new JsonResponse(array('status' => 'OK', 'html' => $html));	    	
	    	exit;
    }
}
