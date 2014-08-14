<?php

namespace LittleBigJoe\Bundle\FrontendBundle\Security\Provider;

use Symfony\Component\Security\Core\User\UserInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseClass;
use LittleBigJoe\Bundle\CoreBundle\Entity\User;

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
		public function connect(UserInterface $user, UserResponseInterface $response)
		{
				$property = $this->getProperty($response);
				$username = $response->getUsername();

				// on connect - get the access token and the user ID
				$service = $response->getResourceOwner()->getName();
		
				$setter = 'set'.ucfirst($service);
				$setter_id = $setter.'Id';
				$setter_token = $setter.'AccessToken';
				$setter_token_secret = $setter.'AccessTokenSecret';
		
				// we "disconnect" previously connected users
				if (null !== $previousUser = $this->userManager->findUserBy(array($property => $username))) 
				{
						$previousUser->$setter_id(null);
						$previousUser->$setter_token(null);
						$previousUser->$setter_token_secret(null);
						$this->userManager->updateUser($previousUser);
				}
		
				//we connect current user
				$user->$setter_id($username);
				$user->$setter_token($response->getAccessToken());
				$user->$setter_token_secret($response->getTokenSecret());
		
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
						$setter_token_secret = $setter.'AccessTokenSecret';
						// create new user here
						$user = $this->userManager->createUser();
						$user->$setter_id($username);
						$user->$setter_token($response->getAccessToken());
						$user->$setter_token_secret($response->getTokenSecret());
						// I have set all requested data with the user's username
						// modify here with relevant data
						$resp = $response->getResponse();

                        $session->set('oauth_gender', '0');
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
		        $user->addRole('ROLE_TWITTER');      
			
				$serviceName = $response->getResourceOwner()->getName();
				$setter_token = 'set' . ucfirst($serviceName) . 'AccessToken';
				$setter_token_secret = 'set' . ucfirst($serviceName) . 'AccessTokenSecret';
		
				// update access token
				$user->$setter_token($response->getAccessToken());
				$user->$setter_token_secret($response->getTokenSecret());
		
				return $user;
		}
}

