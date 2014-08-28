<?php
 
namespace LittleBigJoe\Bundle\FrontendBundle\Security\Provider;
 
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
 
use \BaseFacebook;
use \FacebookApiException;
 
class FacebookProvider implements UserProviderInterface
{
    /**
     * @var \Facebook
     */
    protected $facebook;
    protected $userManager;
    protected $validator;
    protected $container;
 
    public function __construct(BaseFacebook $facebook, $userManager, $validator, $container)
    {
        $this->facebook = $facebook;
        $this->userManager = $userManager;
        $this->validator = $validator;
        $this->container = $container;
    }
 
    public function supportsClass($class)
    {
        return $this->userManager->supportsClass($class);
    }
 
    public function findUserByFbId($fbId)
    {
        return $this->userManager->findUserBy(array('facebookId' => $fbId));
    }

    public function findUserByEmail($email)
    {
        return $this->userManager->findUserBy(array('email' => $email));
    }
 
    public function loadUserByUsername($username)
    {
        $user = $this->findUserByFbId($username);
        $em = $this->container->get('doctrine')->getManager();

        try {
            $fbdata = $this->facebook->api('/me');
        } catch (FacebookApiException $e) {
            throw new UsernameNotFoundException('The user is not authenticated on facebook');
            $fbdata = null;
        }

        if (empty($user)) {
            $user = $this->findUserByEmail($fbdata['email']);
        }
 
        if (!empty($fbdata)) {

            if (empty($user)) {
                $user = $this->userManager->createUser();
                $user->setEnabled(true);
                $user->setPlainPassword($this->randomPassword());
 
                $user->setFBData($fbdata);                
            }
 
            if (count($this->validator->validate($user, 'Facebook'))) {
                throw new UsernameNotFoundException('The facebook user could not be stored');
            }

            // Upload user photo
            $relativePath = 'uploads/users/'.$fbdata['id'].'.jpg';
            $localImage = $this->container->get('kernel')->getRootDir().'/../web/'.$relativePath;
            $image = file_get_contents('http://graph.facebook.com/'.$fbdata['id'].'/picture?type=large');
            $fp = fopen($localImage, "w");
            fwrite($fp, $image);
            fclose($fp);
            $uploadedFile = new UploadedFile(
                $relativePath,
                $fbdata['id'].'.jpg'
            );
            $user->setPhoto($uploadedFile);

            // Stock temporarly user plain password, to send it by email
            $plainPassword = $user->getPlainPassword();
            
            $this->userManager->updateUser($user);
            
            // Create user in MangoPay
            $api = $this->container->get('little_big_joe_mango_pay.api');
            
            // Set default nationality (required for MangoPay)
            $userLanguage = $user->getDefaultLanguage();
            if ($userLanguage == 'fr')
            		$userNationality = 'FR';
            else
            		$userNationality = 'EN';
            
            $mangopayUser = $api->createUser($user->getEmail(), $user->getFirstname(), $user->getLastname(), $user->getIpAddress(), $user->getBirthday()->getTimestamp(), $userNationality, $user->getPersonType(), $user->getId());
            if (!empty($mangopayUser))
            {
	            	if (!empty($mangopayUser->ID))
	            	{
	            			$user->setMangopayUserId($mangopayUser->ID);
	            	}
	            	if (!empty($mangopayUser->CreationDate))
	            	{
		            		$user->setMangopayCreatedAt(new \DateTime('@'.$mangopayUser->CreationDate));
		            		$user->setMangopayUpdatedAt(new \DateTime('@'.$mangopayUser->CreationDate));
	            	}
	            	if (!empty($mangopayUser->UpdateDate))
	            	{
	            			$user->setMangopayUpdatedAt(new \DateTime('@'.$mangopayUser->UpdateDate));
	            	}
	            	$em->persist($user);
	            	$em->flush();
            }
        }
 
        if (empty($user)) {
            throw new UsernameNotFoundException('The user is not authenticated on facebook');
        }
 
        return $user;
    }
 

    /**
     * @return string
     *
     * Generate a random password of 12 caracters
     */
    private function randomPassword()
    {
	    	$alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789@!#&-_()?!";
	    	$pass = array();
	    	$alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
	    	for ($i = 0; $i < 15; $i++) 
	    	{
		    		$n = rand(0, $alphaLength);
		    		$pass[] = $alphabet[$n];
	    	}
	    	return implode($pass);
    }
    
    public function refreshUser(UserInterface $user)
    {
        if (!$this->supportsClass(get_class($user)) || !$user->getFacebookId()) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getFacebookId());
    }
}