<?php

namespace LittleBigJoe\Bundle\FrontendBundle\Security\Provider;

use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseClass;
use LittleBigJoe\Bundle\FrontendBundle\Entity\User;

class FOSUBUserProvider extends BaseClass
{
		/**
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * @var array
     */
    protected $properties;
    protected $container;

    /**
     * Constructor.
     *
     * @param UserManagerInterface $userManager FOSUB user provider.
     * @param array                $properties  Property mapping.
     * @param Container service
     */
		public function __construct($userManager, array $properties, $container)
		{
				$this->userManager = $userManager;
				$this->properties  = $properties;
				$this->container = $container;
		}
	
		/**
		 * {@inheritDoc}
		 */
		public function connect($user, UserResponseInterface $response)
		{
				$property = $this->getProperty($response);
				$username = $response->getUsername();
		
				// on connect - get the access token and the user ID
				$service = $response->getResourceOwner()->getName();
		
				$setter = 'set'.ucfirst($service);
				$setter_id = $setter.'Id';
				$setter_token = $setter.'AccessToken';
		
				// we "disconnect" previously connected users
				if (null !== $previousUser = $this->userManager->findUserBy(array($property => $username))) 
				{
						$previousUser->$setter_id(null);
						$previousUser->$setter_token(null);
						$this->userManager->updateUser($previousUser);
				}
		
				//we connect current user
				$user->$setter_id($username);
				$user->$setter_token($response->getAccessToken());
		
				$this->userManager->updateUser($user);
		}
	
		/**
		 * {@inheritdoc}
		 */
		public function loadUserByOAuthUserResponse(UserResponseInterface $response)
		{
				$session = $this->container->get('request')->getSession();
				$username = $response->getUsername();
				$user = $this->userManager->findUserBy(array($this->getProperty($response) => $username));
								
				// when the user is registrating
				if (null === $user) 
				{
						$service = $response->getResourceOwner()->getName();
						$setter = 'set'.ucfirst($service);
						$setter_id = $setter.'Id';
						$setter_token = $setter.'AccessToken';
						// create new user here
						$user = $this->userManager->createUser();
						$user->$setter_id($username);
						$user->$setter_token($response->getAccessToken());
						// I have set all requested data with the user's username
						// modify here with relevant data
						$resp = $response->getResponse();
						
						if (array_key_exists('name', $response->getResponse()))
						{
								if (preg_match('/[\s]+/', $resp['name']))
								{
										$names = preg_split('/ /', $resp['name']);
										$session->set('oauth_firstname', $names[0]);
										$session->set('oauth_lastname', $names[1]);
								}
								else
								{
										$session->set('oauth_firstname', $resp['name']);
								}
						}
						if (array_key_exists('lang', $response->getResponse()))
								$session->set('oauth_lang', $resp['lang']);
						if (array_key_exists('location', $response->getResponse()))
								$session->set('oauth_location', $resp['location']);
						if (array_key_exists('description', $response->getResponse()))
								$session->set('oauth_bio', $resp['description']);
						if (array_key_exists('url', $response->getResponse()))
								$session->set('oauth_websiteUrl', $resp['url']);
						if (array_key_exists('screen_name', $response->getResponse()))
								$session->set('oauth_twitterUrl', 'https://www.twitter.com/'.$resp['screen_name']);
				}
		
				// if user exists - go with the HWIOAuth way
				$user = parent::loadUserByOAuthUserResponse($response);
		
				$serviceName = $response->getResourceOwner()->getName();
				$setter = 'set' . ucfirst($serviceName) . 'AccessToken';
		
				// update access token
				$user->$setter($response->getAccessToken());
		
				return $user;
		}
}

