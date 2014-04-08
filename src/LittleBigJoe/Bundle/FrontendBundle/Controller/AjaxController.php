<?php

namespace LittleBigJoe\Bundle\FrontendBundle\Controller;

use LittleBigJoe\Bundle\CoreBundle\Entity\ProjectImage;
use LittleBigJoe\Bundle\CoreBundle\Entity\ProjectVideo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use LittleBigJoe\Bundle\CoreBundle\Entity\ProjectLike;
use LittleBigJoe\Bundle\CoreBundle\Entity\Entry;
use LittleBigJoe\Bundle\CoreBundle\Entity\EntryComment;
use LittleBigJoe\Bundle\CoreBundle\Entity\Comment;
use LittleBigJoe\Bundle\CoreBundle\Entity\ProjectHelp;

/**
 * Ajax controller.
 *
 * @Route("/ajax", options={ "i18n": false })
 */
class AjaxController extends Controller
{
    /**
     * Set media as default project media
     *
     * @Route("/highlight-product-item", name="littlebigjoe_frontendbundle_ajax_highlight_product_gallery_item")
     * @Method("POST")
     * @Template()
     */
    public function highlightProductItemAction(Request $request)
    {
        return $this->highlightItemAction($request, 'productMedias');
    }

    /**
     * Set media as default project media
     *
     * @Route("/highlight-item", name="littlebigjoe_frontendbundle_ajax_highlight_gallery_item")
     * @Method("POST")
     * @Template()
     */
    public function highlightItemAction(Request $request, $sessionKey = 'projectMedias')
    {
        $em = $this->getDoctrine()->getManager();
        $projectMedias = $this->getRequest()->getSession()->get($sessionKey, array());
        $mediaType = $this->get('request')->request->get('type');
        $mediaId = $this->get('request')->request->get('id');
        $return = array('status' => 'KO');

        if (!empty($mediaType) && !empty($mediaId))
        {
            foreach ($projectMedias as $key => $projectMedia)
            {
                if ($projectMedia['id'] == $mediaId && $projectMedia['type'] == $mediaType)
                {
                    if ($projectMedias[$key]['highlighted'])
                    {
                        $projectMedias[$key]['highlighted'] = false;
                    }
                    else
                    {
                        $projectMedias[$key]['highlighted'] = true;
                    }

                    $this->getRequest()->getSession()->set($sessionKey, $projectMedias);
                    $return = array('status' => 'OK', 'toHighlight' => $projectMedias[$key]['highlighted']);
                }
                else
                {
                    $projectMedias[$key]['highlighted'] = false;
                }
            }
        }

        // Make sure no code is executed after it
        return new JsonResponse($return);
        exit;
    }

    /**
     * Insert image
     *
     * @Route("/add-product-image", name="littlebigjoe_frontendbundle_ajax_insert_product_image")
     * @Method("POST")
     * @Template()
     */
    public function addProductImageAction(Request $request)
    {
        return $this->addImageAction($request, 'productMedias');
    }

    /**
     * Insert image
     *
     * @Route("/add-image", name="littlebigjoe_frontendbundle_ajax_insert_image")
     * @Method("POST")
     * @Template()
     */
    public function addImageAction(Request $request, $sessionKey = 'projectMedias')
    {
        $em = $this->getDoctrine()->getManager();
        $projectMedias = $this->getRequest()->getSession()->get($sessionKey, array());
        $file = $_FILES['selectGalleryImages'];
        $imageData = array('status' => 'KO');

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
        if (!in_array($file['type'], $this->container->getParameter('allowed_image_mime_types')))
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
        $webDir = $this->get('kernel')->getRootDir().'/../web/';
        $dirName = 'uploads/tmp/user/'.preg_replace('/[^a-z0-9_\-]/i', '_', $currentUser->getEmail());
        if (!file_exists($dirName))
        {
            mkdir($dirName, 0755);
        }
        $absolutePath = $webDir.$dirName.'/';
        $relativePath = $this->getRequest()->getSchemeAndHttpHost().'/uploads/tmp/user/'.preg_replace('/[^a-z0-9_\-]/i', '_', $currentUser->getEmail()).'/';
        @move_uploaded_file($file['tmp_name'], $absolutePath.$tmpName);

        $image = new ProjectImage();
        $image->setName($tmpName);
        $image->setPath($dirName.'/'.$tmpName);
        $image->setHighlighted(false);
        $em->persist($image);
        $em->flush();

        $imageData = array(
            'id' => $image->getId(),
            'image' => '/'.$image->getPath(),
            'highlighted' => $image->getHighlighted(),
            'status' => 'OK'
        );

        // Store the media ID in session for easier access
        $projectMedias['image_'.$image->getId()] = array(
            'type' => 'image',
            'id' => $image->getId(),
            'image' => '/'.$image->getPath(),
            'highlighted' => $image->getHighlighted()
        );
        $this->getRequest()->getSession()->set($sessionKey, $projectMedias);

        // Make sure no code is executed after it
        return new JsonResponse($imageData);
        exit;
    }

    /**
     * Insert video
     *
     * @Route("/add-product-video", name="littlebigjoe_frontendbundle_ajax_insert_product_video")
     * @Method("POST")
     * @Template()
     */
    public function addProductVideoAction(Request $request)
    {
        return $this->addVideoAction($request, 'productMedias');
    }

    /**
     * Insert video
     *
     * @Route("/add-video", name="littlebigjoe_frontendbundle_ajax_insert_video")
     * @Method("POST")
     * @Template()
     */
    public function addVideoAction(Request $request, $sessionKey = 'projectMedias')
    {
        $em = $this->getDoctrine()->getManager();
        $projectMedias = $this->getRequest()->getSession()->get($sessionKey, array());
        $videoUrl = $this->get('request')->request->get('videoUrl');
        $videoData = array('status' => 'KO');

        $provider = $this->get('littlebigjoe.media.provider.factory')->getProviderByUrl($videoUrl);

        if (!empty($provider))
        {
            $video = $provider->getVideo($videoUrl);
            $em->persist($video);
            $em->flush();

            $videoData = array(
                'id' => $video->getId(),
                'image' => $video->getThumbUrl(),
                'highlighted' => $video->getHighlighted(),
                'status' => 'OK'
            );

            // Store the media ID in session for easier access
            $projectMedias['video_'.$video->getId()] = array(
                'type' => 'video',
                'id' => $video->getId(),
                'image' => $video->getThumbUrl(),
                'highlighted' => $video->getHighlighted()
            );
            $this->getRequest()->getSession()->set($sessionKey, $projectMedias);
        }

        // Make sure no code is executed after it
        return new JsonResponse($videoData);
        exit;
    }

    /**
     * Remove gallery item
     *
     * @Route("/remove-product-gallery-item", name="littlebigjoe_frontendbundle_ajax_remove_product_gallery_item")
     * @Method("POST")
     * @Template()
     */
    public function removeProductGalleryItemAction(Request $request)
    {
        return $this->removeGalleryItemAction($request, 'productMedias');
    }

    /**
     * Remove gallery item
     *
     * @Route("/remove-gallery-item", name="littlebigjoe_frontendbundle_ajax_remove_gallery_item")
     * @Method("POST")
     * @Template()
     */
    public function removeGalleryItemAction(Request $request, $sessionKey = 'projectMedias')
    {
        $em = $this->getDoctrine()->getManager();
        $projectMedias = $this->getRequest()->getSession()->get($sessionKey, array());
        $mediaType = $this->get('request')->request->get('type');
        $mediaId = $this->get('request')->request->get('id');
        $return = array('status' => 'KO');

        if (!empty($mediaType) && !empty($mediaId))
        {
            if ($mediaType == 'video')
            {
                $video = $em->getRepository('LittleBigJoeCoreBundle:ProjectVideo')->find($mediaId);
                if ($video instanceof ProjectVideo)
                {
                    // Remove the media ID in session
                    unset($projectMedias['video_'.$video->getId()]);
                    $this->getRequest()->getSession()->set($sessionKey, $projectMedias);

                    $em->remove($video);
                    $em->flush();

                    $return = array('status' => 'OK');
                }
            }
            else if ($mediaType == 'image')
            {
                $image = $em->getRepository('LittleBigJoeCoreBundle:ProjectImage')->find($mediaId);
                if ($image instanceof ProjectImage)
                {
                    // Remove the media ID in session
                    unset($projectMedias['image_'.$image->getId()]);
                    $this->getRequest()->getSession()->set($sessionKey, $projectMedias);

                    $em->remove($image);
                    $em->flush();

                    $return = array('status' => 'OK');
                }
            }
        }

        // Make sure no code is executed after it
        return new JsonResponse($return);
        exit;
    }

    /**
     * Search product type
     *
     * @Route("/search-product-type", name="littlebigjoe_frontendbundle_ajax_search_product_type")
     * @Method("GET")
     * @Template()
     */
    public function searchProductTypeAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $productType = strtolower($this->get('request')->query->get('productType'));
        $predictions = array();

        $productTypes = $em->getRepository('LittleBigJoeCoreBundle:ProductType')->findEquivalents($productType, $request->getLocale());
        if (!empty($productTypes))
        {
            foreach ($productTypes as $productType)
                array_push($predictions, array('value' => $productType['name']));
        }

        // Make sure no code is executed after it
        return new JsonResponse($predictions);
        exit;
    }

    /**
     * Search location
     *
     * @Route("/search-location", name="littlebigjoe_frontendbundle_ajax_search_location")
     * @Method("GET")
     * @Template()
     */
    public function searchLocationAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $location = strtolower($this->get('request')->query->get('location'));
        $predictions = array();

        $ch = curl_init();
        $url = 'https://maps.googleapis.com/maps/api/place/autocomplete/json?input='.htmlspecialchars($location).
               '&types=geocode&language='.$request->getLocale().'&sensor=true&key='.$this->container->getParameter('api_google_key');
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($ch);
        $data = json_decode($data);
        curl_close($ch);

        if (!empty($data->predictions))
        {
            foreach ($data->predictions as $prediction)
            {
                $terms = array();
                foreach ($prediction->terms as $term)
                    array_push($terms, $term->value);

                array_push($predictions, array(
                    'year' => '1950',
                    'value' => $prediction->description,
                    'tokens' => $terms
                ));
            }
        }

        // Make sure no code is executed after it
        return new JsonResponse($predictions);
        exit;
    }

    /**
     * Get user notifications
     *
     * @Route("/get-notifications", name="littlebigjoe_frontendbundle_ajax_get_notifications")
     * @Method("POST")
     * @Template()
     */
    public function getNotificationsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        $currentUser = $this->get('security.context')->getToken()->getUser();
        // If the current user is not logged, redirect him to login page
        if (!is_object($currentUser))
        {
            return new JsonResponse(array('status' => 'KO'));
        }
    
        $nbNotifs = $em->getRepository('LittleBigJoeCoreBundle:Notification')->getUnreadNotifications($currentUser->getId());
        if (empty($nbNotifs))
        {
            $nbNotifs = 0;
        }
        
        // Make sure no code is executed after it
        return new JsonResponse(array('status' => 'OK', 'nb_notifs' => $nbNotifs));
        exit;
    }
        
    /**
     * Save report project form
     *
     * @Route("/report-project", name="littlebigjoe_frontendbundle_ajax_report_project")
     * @Method("POST")
     * @Template()
     */
    public function reportProjectAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $projectId = (int)$this->get('request')->request->get('projectId');
        $formData = $this->get('request')->request->get('data');
        $options = array(
            'reportReasons' => array(
                $this->get('translator')->trans('Intellectual property infringement'),
                $this->get('translator')->trans('I think this project should not be on LittleBigJoe'),
                $this->get('translator')->trans('This is spam'),
                $this->get('translator')->trans('Other')
            )
        );
        
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
    
        $currentUser = $this->get('security.context')->getToken()->getUser();
        // If the current user is not logged, redirect him to login page
        if (!is_object($currentUser))
        {
            $this->get('session')->getFlashBag()->add(
                'notice',
                'You must be logged in to submit this form'
            );
             
            // Force base url to make sure environment is not specified in the URL
            $this->get('router')->getContext()->setBaseUrl('');
            $request->getSession()->set('_security.main.target_path', $this->generateUrl('littlebigjoe_frontendbundle_brand_show', array('slug' => $brand->getSlug())));
            return new JsonResponse(array('status' => 'KO BRAND'));
        }
    
        $email = \Swift_Message::newInstance()
					->setContentType('text/html')
					->setSubject($this->get('translator')->trans('A project has been reported'))
					->setFrom($this->container->getParameter('default_email_address'))
					->setTo($this->container->getParameter('default_email_address'))
					->setBody(
						$this->renderView('LittleBigJoeFrontendBundle:Email:report_project.html.twig', array(
							'user' => $currentUser,
						    'userIp' => $_SERVER['REMOTE_ADDR'],
						    'reportReasons' => $options['reportReasons'],
						    'report' => $formData,
							'project' => $project,
        				    'url' => $this->get('request')->getSchemeAndHttpHost()
						), 'text/html')
					);
		$this->get('mailer')->send($email);
    
        // Make sure no code is executed after it
        return new JsonResponse(array('status' => 'OK'));
        exit;
    }
    
    
    /**
     * Save help project form
     *
     * @Route("/help-project", name="littlebigjoe_frontendbundle_ajax_help_project")
     * @Method("POST")
     * @Template()
     */
    public function helpProjectAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $projectId = (int)$this->get('request')->request->get('projectId');
        $formData = $this->get('request')->request->get('data');
        
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
    
        $currentUser = $this->get('security.context')->getToken()->getUser();
        // If the current user is not logged, redirect him to login page
        if (!is_object($currentUser))
        {
            $this->get('session')->getFlashBag()->add(
                'notice',
                'You must be logged in to submit this form'
            );
             
            // Force base url to make sure environment is not specified in the URL
            $this->get('router')->getContext()->setBaseUrl('');
            $request->getSession()->set('_security.main.target_path', $this->generateUrl('littlebigjoe_frontendbundle_brand_show', array('slug' => $brand->getSlug())));
            return new JsonResponse(array('status' => 'KO BRAND'));
        }
    
        // Create new project help
        $projectHelp = new ProjectHelp();
    	$projectHelp->setProject($project);
    	$projectHelp->setUser($currentUser);
    	$projectHelp->setPrice($formData[0]['value']);
    	$projectHelp->setCurrency($formData[1]['value']);
    	$projectHelp->setQuantity($formData[2]['value']);
    	$projectHelp->setReason($formData[3]['value']);
    	
    	// Generate social network message
    	$projectUrl = $this->generateUrl('littlebigjoe_frontendbundle_project_show', array('slug' => $project->getSlug()), true);
    	$brandUrl = '';
    	$status = $project->getStatus();
    	if ($status == '1')
    	{
    	    $message = $this->get('translator')->trans('I just voted for project %project%', array('%project%' => $project->getName()));
    	}
    	else
    	{
    	    $message = $this->get('translator')->trans('I just financed project %project%', array('%project%' => $project->getName()));
    	}

    	$brandFacebookUrl = $project->getBrand()->getFacebookUrl();
    	$brandTwitterUrl = $project->getBrand()->getTwitterUrl();
    	if (!empty($brandFacebookUrl))
    	{
    	    $brandUrl = $brandFacebookUrl;
    	}
    	elseif (!empty($brandTwitterUrl))
    	{
    	    $brandUrl = $brandTwitterUrl;
    	}
    	if (!empty($brandUrl))
    	{
    	   $message .= ' '.$this->get('translator')->trans('for %brand%%url%', array(
    	       '%brand%' => $project->getBrand(),
    	       '%url%' => ' '.$brandUrl
    	   ));
    	}
    	
    	$message .= ' '.$this->get('translator')->trans('on @LittleBigJoe %url%', array(
    	    '%url%' => $projectUrl
    	));
    		
    	// Send message
    	if ($formData[4]['name'] == 'helpproject[sharedFacebook]')
    	{
    	    $projectHelp->setSharedFacebook($formData[4]['value']);
    	        	    
    	    $this->container->get('fos_facebook.api')->api('/me/feed', 'POST',    	    
    	        array(
    	            'link' => $projectUrl,
    	            'message' => $message
    	        )
    	    );
    	}    	
    	
    	if ($formData[4]['name'] == 'helpproject[sharedTwitter]')
    	{
    	    $projectHelp->setSharedTwitter($formData[4]['value']);
    	    
    	    $client = new \Guzzle\Service\Client('https://api.twitter.com/{version}', array('version' => '1.1'));
            $oauth  = new \Guzzle\Plugin\Oauth\OauthPlugin(array(
                'consumer_key' => $this->container->getParameter('api_twitter_key'),
                'consumer_secret' => $this->container->getParameter('api_twitter_secret'),
                'token' => $this->get('security.context')->getToken()->getUser()->getTwitterAccessToken(),
                'token_secret' => $this->get('security.context')->getToken()->getUser()->getTwitterAccessTokenSecret()
            ));
            $client->addSubscriber($oauth);
            
            $client->post('statuses/update.json', null, array(
                'status' => $message
            ))->send();
    	}
    	// Save project help in DB
    	$em->persist($projectHelp);
    	$em->flush();
    
        // Make sure no code is executed after it
        return new JsonResponse(array('status' => 'OK'));
        exit;
    }    
    
    /**
     * Follow a brand
     *
     * @Route("/follow-brand", name="littlebigjoe_frontendbundle_ajax_follow_brand")
     * @Method("POST")
     * @Template()
     */
    public function followBrandAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $brandId = (int)$this->get('request')->request->get('brandId');
    
        // If it's not a correct brand id
        if (empty($brandId))
        {
            return new JsonResponse(array('status' => 'KO ID'));
        }
    
        $brand = $em->getRepository('LittleBigJoeCoreBundle:Brand')->find($brandId);
    
        // If the brand doesn't exist
        if (empty($brand))
        {
        			 return new JsonResponse(array('status' => 'KO BRAND'));
        }
    
        $currentUser = $this->get('security.context')->getToken()->getUser();
        // If the current user is not logged, redirect him to login page
        if (!is_object($currentUser))
        {
            $this->get('session')->getFlashBag()->add(
                'notice',
                'You must be logged in to follow this brand'
            );
            	
            // Force base url to make sure environment is not specified in the URL
            $this->get('router')->getContext()->setBaseUrl('');
            $request->getSession()->set('_security.main.target_path', $this->generateUrl('littlebigjoe_frontendbundle_brand_show', array('slug' => $brand->getSlug())));
            return new JsonResponse(array('status' => 'KO BRAND'));
        }
    
        // If user has already follow this brand
        $followedBrands = $currentUser->getFollowedBrands();
        if (empty($followedBrands))
        {
            $followedBrands = array();
        }
    
        foreach ($followedBrands as $followedBrand)
        {
            if ($brand->getId() == $followedBrand->getId())
            {
                return new JsonResponse(array('status' => 'KO FOLLOW'));
            }
        }
    
        // Set followed brand
        $currentUser->addFollowedBrand($brand);
    
        // Save follow in DB
        $em->persist($currentUser);
        $em->flush();
    
        // Make sure no code is executed after it
        return new JsonResponse(array('status' => 'OK'));
        exit;
    }
    
    /**
     * Unfollow a brand
     *
     * @Route("/unfollow-brand", name="littlebigjoe_frontendbundle_ajax_unfollow_brand")
     * @Method("POST")
     * @Template()
     */
    public function unfollowBrandAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $brandId = (int)$this->get('request')->request->get('brandId');
    
        // If it's not a correct brand id
        if (empty($brandId))
        {
            return new JsonResponse(array('status' => 'KO ID'));
        }
    
        $brand = $em->getRepository('LittleBigJoeCoreBundle:Brand')->find($brandId);
    
        // If the brand doesn't exist
        if (empty($brand))
        {
            return new JsonResponse(array('status' => 'KO BRAND'));
        }
    
        $currentUser = $this->get('security.context')->getToken()->getUser();
        // If the current user is not logged, redirect him to login page
        if (!is_object($currentUser))
        {
            $this->get('session')->getFlashBag()->add(
                'notice',
                'You must be logged in to follow this brand'
            );
             
            // Force base url to make sure environment is not specified in the URL
            $this->get('router')->getContext()->setBaseUrl('');
            $request->getSession()->set('_security.main.target_path', $this->generateUrl('littlebigjoe_frontendbundle_brand_show', array('slug' => $brand->getSlug())));
            return new JsonResponse(array('status' => 'KO BRAND'));
        }
    
        // If user has already follow this brand
        $followedBrands = $currentUser->getFollowedBrands();
        if (empty($followedBrands))
        {
            $followedBrands = array();
        }
            
        foreach ($followedBrands as $followedBrand)
        {
            if ($brand->getId() == $followedBrand->getId())
            {
                // Unfollow brand
                $currentUser->removeFollowedBrand($brand);
    
                // Save unfollow in DB
                $em->persist($currentUser);
                $em->flush();
    
                // Make sure no code is executed after it
                return new JsonResponse(array('status' => 'OK'));
                exit;
            }
        }
    
        return new JsonResponse(array('status' => 'KO UNFOLLOW'));
        exit;
    }
    
    /**
     * Follow a user
     *
     * @Route("/follow-user", name="littlebigjoe_frontendbundle_ajax_follow_user")
     * @Method("POST")
     * @Template()
     */
    public function followUserAction(Request $request)
    {    		
    		$em = $this->getDoctrine()->getManager();
    		$userId = (int)$this->get('request')->request->get('userId');

    		// If it's not a correct user id
    		if (empty($userId))
    		{
    		     return new JsonResponse(array('status' => 'KO ID'));
    		}
    		
    		$user = $em->getRepository('LittleBigJoeCoreBundle:User')->find($userId);
    		
    		// If the user doesn't exist
    		if (empty($user))
    		{
    			 return new JsonResponse(array('status' => 'KO USER'));
    		}    		
    		
    		$currentUser = $this->get('security.context')->getToken()->getUser();
			// If the current user is not logged, redirect him to login page
			if (!is_object($currentUser))
			{
					$this->get('session')->getFlashBag()->add(
							'notice',
							'You must be logged in to follow this user'
					);						
					
					// Force base url to make sure environment is not specified in the URL
					$this->get('router')->getContext()->setBaseUrl('');
					$request->getSession()->set('_security.main.target_path', $this->generateUrl('littlebigjoe_frontendbundle_user_show', array('id' => $user->getId())));												
					return new JsonResponse(array('status' => 'KO USER'));
			}
    		    		
    		// If user has already follow this user
    		$followedUsers = $currentUser->getFollowedUsers();
    		if (empty($followedUsers))
    		{
    		    $followedUsers = array();
    		}    		
    		
    		foreach ($followedUsers as $followedUser)
    		{
    		    if ($user->getId() == $followedUser->getId())
    		    {
    		        return new JsonResponse(array('status' => 'KO FOLLOW'));
    		    }
    		}
    		
    		// Set followed user
    		$currentUser->addFollowedUser($user);
    		
    		// Save follow in DB
    		$em->persist($currentUser);
    		$em->flush();
    		
    		// Make sure no code is executed after it
    		return new JsonResponse(array('status' => 'OK'));
    		exit;
    }
    
    /**
     * Unfollow a user
     *
     * @Route("/unfollow-user", name="littlebigjoe_frontendbundle_ajax_unfollow_user")
     * @Method("POST")
     * @Template()
     */
    public function unfollowUserAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $userId = (int)$this->get('request')->request->get('userId');
    
        // If it's not a correct user id
        if (empty($userId))
        {
            return new JsonResponse(array('status' => 'KO ID'));
        }
    
        $user = $em->getRepository('LittleBigJoeCoreBundle:User')->find($userId);
    
        // If the user doesn't exist
        if (empty($user))
        {
        			 return new JsonResponse(array('status' => 'KO USER'));
        }
    
        $currentUser = $this->get('security.context')->getToken()->getUser();
        // If the current user is not logged, redirect him to login page
        if (!is_object($currentUser))
        {
            $this->get('session')->getFlashBag()->add(
                'notice',
                'You must be logged in to follow this user'
            );
            	
            // Force base url to make sure environment is not specified in the URL
            $this->get('router')->getContext()->setBaseUrl('');
            $request->getSession()->set('_security.main.target_path', $this->generateUrl('littlebigjoe_frontendbundle_user_show', array('id' => $user->getId())));
            return new JsonResponse(array('status' => 'KO USER'));
        }
    
        // If user has already follow this user
        $followedUsers = $currentUser->getFollowedUsers();
        if (empty($followedUsers))
        {
            $followedUsers = array();
        }
    
        foreach ($followedUsers as $followedUser)
        {
            if ($user->getId() == $followedUser->getId())
            {
                // Unfollow user
                $currentUser->removeFollowedUser($user);
            
                // Save unfollow in DB
                $em->persist($currentUser);
                $em->flush();
            
                // Make sure no code is executed after it
                return new JsonResponse(array('status' => 'OK'));
                exit;
            }
        }
    
        return new JsonResponse(array('status' => 'KO UNFOLLOW'));
        exit;        
    }
    
    /**
     * Like a project
     *
     * @Route("/like-project", name="littlebigjoe_frontendbundle_ajax_like_project")
     * @Method("POST")
     * @Template()
     */
    public function likeProjectAction(Request $request)
    {    		
    		$em = $this->getDoctrine()->getManager();
    		$projectId = (int)$this->get('request')->request->get('projectId');

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
    		
    		$currentUser = $this->get('security.context')->getToken()->getUser();
				// If the current user is not logged, redirect him to login page
				if (!is_object($currentUser))
				{
						$this->get('session')->getFlashBag()->add(
								'notice',
								'You must be logged in to like this project'
						);						
						
						// Force base url to make sure environment is not specified in the URL
						$this->get('router')->getContext()->setBaseUrl('');
						$request->getSession()->set('_security.main.target_path', $this->generateUrl('littlebigjoe_frontendbundle_project_show', array('slug' => $project->getSlug())).'?likePopup=true');												
						return new JsonResponse(array('status' => 'KO USER'));
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
     * Fund a project
     *
     * @Route("/fund-project", name="littlebigjoe_frontendbundle_ajax_fund_project")
     * @Method("POST")
     * @Template()
     */
    public function fundProjectAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $projectId = (int)$this->get('request')->request->get('projectId');
    
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
    
        $currentUser = $this->get('security.context')->getToken()->getUser();
        // If the current user is not logged, redirect him to login page
        if (!is_object($currentUser))
        {
            $this->get('session')->getFlashBag()->add(
                'notice',
                'You must be logged in to like this project'
            );
    
            // Force base url to make sure environment is not specified in the URL
            $this->get('router')->getContext()->setBaseUrl('');
            $request->getSession()->set('_security.main.target_path', $this->generateUrl('littlebigjoe_frontendbundle_project_show', array('slug' => $project->getSlug())).'?fundingPopup=true');
            return new JsonResponse(array('status' => 'KO USER'));
        }
    
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
    public function entryProjectAction(Request $request)
    {
	    	$em = $this->getDoctrine()->getManager();
	    	$formData = $this->get('request')->request->get('entry');

	    	// If there's no title/content/public
	    	if (empty($formData) || empty($formData['title']) || empty($formData['content']) || empty($formData['project']) || $formData['isPublic'] == null)
	    	{
	    			return new JsonResponse(array('status' => 'KO FIELD'));
	    	}
	    		    	
	    	$project = $em->getRepository('LittleBigJoeCoreBundle:Project')->find($formData['project']);
	    		    	
	    	$currentUser = $this->get('security.context')->getToken()->getUser();
	    	// If the current user is not logged, redirect him to login page
	    	if (!is_object($currentUser))
	    	{
		    		$this->get('session')->getFlashBag()->add(
		    				'notice',
		    				'You must be logged in to like this project'
		    		);
		    	
		    		// Force base url to make sure environment is not specified in the URL
		    		$this->get('router')->getContext()->setBaseUrl('');
		    		$request->getSession()->set('_security.main.target_path', $this->generateUrl('littlebigjoe_frontendbundle_project_show', array('slug' => $project->getSlug())));
		    		return new JsonResponse(array('status' => 'KO USER'));
	    	}
	    		
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
	    			'created_at' => $entry->getCreatedAt()->format('m/d/Y h:i'),
	    			'content' => $entry->getContent()
	    	);
	    
	    	// Make sure no code is executed after it
	    	return new JsonResponse(array('status' => 'OK', 'entry' => $entryJson));
	    	exit;
    }    
    
    /**
     * Comment an entry
     *
     * @Route("/comment-project-entry", name="littlebigjoe_frontendbundle_ajax_entry_comment_project")
     * @Method("POST")
     * @Template()
     */
    public function entryCommentProjectAction(Request $request)
    {
	    	$em = $this->getDoctrine()->getManager();
	    	$formData = $this->get('request')->request->get('entrycomment');
	    	 
	    	// If there's no content
	    	if (empty($formData) || empty($formData['content']) || empty($formData['project']) || empty($formData['entry']))
	    	{
	    			return new JsonResponse(array('status' => 'KO FIELD'));
	    	}
	    
	    	$project = $em->getRepository('LittleBigJoeCoreBundle:Project')->find($formData['project']);
	    	$entry = $em->getRepository('LittleBigJoeCoreBundle:Entry')->find($formData['entry']);
	    
	    	$currentUser = $this->get('security.context')->getToken()->getUser();
	    	// If the current user is not logged, redirect him to login page
	    	if (!is_object($currentUser))
	    	{
		    		$this->get('session')->getFlashBag()->add(
		    				'notice',
		    				'You must be logged in to comment this entry'
		    		);
		    		 
		    		// Force base url to make sure environment is not specified in the URL
		    		$this->get('router')->getContext()->setBaseUrl('');
		    		$request->getSession()->set('_security.main.target_path', $this->generateUrl('littlebigjoe_frontendbundle_project_show', array('slug' => $project->getSlug())));
		    		return new JsonResponse(array('status' => 'KO USER'));
	    	}
	    	 
	    	// If the project doesn't exist or entry doesn't exists
	    	if (empty($project) || empty($entry))
	    	{
	    			return new JsonResponse(array('status' => 'KO PROJECT'));
	    	}
	    
	    	$entryComment = new EntryComment();
	    	$entryComment->setEntry($entry);
	    	$entryComment->setUser($currentUser);
	    	$entryComment->setIsVisible($entry->getIsPublic());
	    	$entryComment->setContent($formData['content']);
	    
	    	// Save entry comment in DB
	    	$em->persist($entryComment);
	    	$em->flush();
	    
	    	$commentJson = array(
	    			'user_name' => (string)$currentUser,
	    			'user_id' => $currentUser->getId(),
	    			'created_at' => $entryComment->getCreatedAt()->format('m/d/Y h:i'),
	    			'content' => $entryComment->getContent()
	    	);
	    
	    	// Make sure no code is executed after it
	    	return new JsonResponse(array('status' => 'OK', 'comment' => $commentJson));
	    	exit;
    }
    
    /**
     * Comment a project
     *
     * @Route("/comment-project", name="littlebigjoe_frontendbundle_ajax_comment_project")
     * @Method("POST")
     * @Template()
     */
    public function commentProjectAction(Request $request)
    {
    		$em = $this->getDoctrine()->getManager();
	    	$formData = $this->get('request')->request->get('comment');
	    
    		// If there's no content
	    	if (empty($formData) || empty($formData['content']) || empty($formData['project']))
	    	{
	    			return new JsonResponse(array('status' => 'KO FIELD'));
	    	}
	    	
	    	$project = $em->getRepository('LittleBigJoeCoreBundle:Project')->find($formData['project']);
	    	
	    	$currentUser = $this->get('security.context')->getToken()->getUser();
	    	// If the current user is not logged, redirect him to login page
	    	if (!is_object($currentUser))
	    	{
		    		$this->get('session')->getFlashBag()->add(
		    				'notice',
		    				'You must be logged in to like this project'
		    		);
		    		 
		    		// Force base url to make sure environment is not specified in the URL
		    		$this->get('router')->getContext()->setBaseUrl('');
		    		$request->getSession()->set('_security.main.target_path', $this->generateUrl('littlebigjoe_frontendbundle_project_show', array('slug' => $project->getSlug())));
		    		return new JsonResponse(array('status' => 'KO USER'));
	    	}
	    	 
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
	    			'created_at' => $comment->getCreatedAt()->format('m/d/Y h:i'),
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
    		$dirName = $this->get('kernel')->getRootDir().'/../web/uploads/tmp/user/'.preg_replace('/[^a-z0-9_\-]/i', '_', $currentUser->getEmail());
    		if (!file_exists($dirName))
    		{
    				mkdir($dirName, 0755);
    		}
    		$absolutePath = $dirName.'/';
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
    		$dirName = $this->get('kernel')->getRootDir().'/../web/uploads/tmp/user/'.preg_replace('/[^a-z0-9_\-]/i', '_', $currentUser->getEmail());
    		if (!file_exists($dirName))
    		{
    				mkdir($dirName, 0755);
    		}
    		$absolutePath = $dirName.'/';
    		$relativePath = $this->getRequest()->getSchemeAndHttpHost().'/uploads/tmp/user/'.preg_replace('/[^a-z0-9_\-]/i', '_', $currentUser->getEmail()).'/';
    		@move_uploaded_file($file['tmp_name'], $absolutePath.$tmpName);
    		
    		// Generate the code that will be added to CKEditor  classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" 
    		$html = '<object type="application/x-shockwave-flash" data="/bundles/littlebigjoefrontend/flash/dewplayer/dewplayer-mini.swf" width="160" height="20" id="player_'.$tmpName.'" name="player_'.$tmpName.'"><param name="wmode" value="transparent" /><param name="movie" value="/bundles/littlebigjoefrontend/flash/dewplayer/dewplayer-mini.swf" /><param name="flashvars" value="showtime=1&amp;mp3='.$relativePath.$tmpName.'" /></object>';
    		
	    	// Make sure no code is executed after it
	    	return new JsonResponse(array('status' => 'OK', 'html' => $html));	    	
	    	exit;
    }
}
