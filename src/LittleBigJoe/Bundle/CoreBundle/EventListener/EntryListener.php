<?php

namespace LittleBigJoe\Bundle\CoreBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use LittleBigJoe\Bundle\CoreBundle\Entity\Entry;
use LittleBigJoe\Bundle\CoreBundle\Entity\Project;
use LittleBigJoe\Bundle\CoreBundle\Entity\Notification;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\Event;
use Symfony\Component\DependencyInjection\ContainerAware;

class EntryListener extends ContainerAware
{
	protected $container;
	
	public function __construct(ContainerInterface $container)
	{
		$this->container = $container;
	}
	
	public function postPersist(LifecycleEventArgs $args)
	{				
		$entry = $args->getEntity();
		$em = $args->getEntityManager();
									
		if ($entry instanceof Entry) 
		{	
			$project = $entry->getProject();
			
			if ($project instanceof Project && $entry->getIsPublic())
			{
    		    // Get followers
    		    $followersIds = array();
    		    $brandFollowers = $project->getBrand()->getFollowers();
    		    if (!empty($brandFollowers))
    		    {
    		        foreach ($brandFollowers as $brandFollower)
    		        {
    		            if (!in_array($brandFollower->getId(), $followersIds))
    		            {
    		                $followersIds[] = $brandFollower->getId();
    		            }
    		        }
    		    }
    		    $userFollowers = $project->getUser()->getFollowers();
    		    if (!empty($userFollowers))
    		    {
    		        foreach ($userFollowers as $userFollower)
    		        {
    		            if (!in_array($userFollower->getId(), $followersIds))
    		            {
    		                $followersIds[] = $userFollower->getId();
    		            }
    		        }
    		    }
    		    
    		    // Generate notifications
    		    if (!empty($followersIds))
    		    {
    		        foreach ($followersIds as $followerId)
    		        {
    		            $follower = $em->getRepository('LittleBigJoeCoreBundle:User')->find($followerId);
    		             
    		            $notification = new Notification();
    		            $notification->setUser($follower);
    		            $notification->setProject($project);
    		            $notification->setEntry($entry);
    		            $notification->setType(2);
    		             
    		            $em->persist($notification);
    		            $em->flush();
    		        }			    
    			}
			}
		}					
	}
}