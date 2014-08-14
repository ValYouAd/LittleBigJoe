<?php

namespace LittleBigJoe\Bundle\FrontendBundle\EventListener;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Mailer\MailerInterface;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\SecurityContext;

class ProfileListener implements EventSubscriberInterface
{
    protected $container;
    protected $securityContext;

    public function __construct(ContainerInterface $container, SecurityContext $securityContext)
    {
        $this->container = $container;
        $this->securityContext = $securityContext;
    }

    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::PROFILE_EDIT_SUCCESS => 'onProfileEditSuccess',
        );
    }

    public function onProfileEditSuccess(FormEvent $event)
    {
        /** @var $user \FOS\UserBundle\Model\UserInterface */
        $user = $event->getForm()->getData();
        $em = $this->container->get('doctrine')->getManager();
        $userManager = $this->container->get('fos_user.user_manager');

        // Upload user photo
        if ($user->getPhoto() != null) {
            $evm = $em->getEventManager();
            $uploadableManager = $this->container->get('stof_doctrine_extensions.uploadable.manager');
            $evm->removeEventListener(array('postFlush'), $uploadableManager->getUploadableListener());
            $uploadableManager->markEntityToUpload($user, $user->getPhoto());
        }

        $userManager->updateUser($user);

        // Update user in MangoPay
        $api = $this->container->get('little_big_joe_mango_pay.api');
        
        // Set default nationality (required for MangoPay)
        $userLanguage = $user->getDefaultLanguage();
        if ($userLanguage == 'fr')
        		$userNationality = 'FR';
        else
        		$userNationality = 'EN';
        
        $mangopayUser = $api->updateUser($user->getMangopayUserId(), $user->getEmail(), $user->getFirstname(), $user->getLastname(), $user->getBirthday()->getTimestamp(), $userNationality, $user->getId());
        if (!empty($mangopayUser))
        {
	        	if (!empty($mangopayUser->UpdateDate))
	        	{
	        			$user->setMangopayUpdatedAt(new \DateTime('@'.$mangopayUser->UpdateDate));
	        	}
	        	$em->persist($user);
	        	$em->flush();
        }

        $request = $event->getRequest();
        $request->setLocale($this->securityContext->getToken()->getUser()->getDefaultLanguage());
        $request->getSession()->set('_locale', $this->securityContext->getToken()->getUser()->getDefaultLanguage());

        if (null === $response = $event->getResponse()) {
            $url = $this->container->get('router')->generate('fos_user_registration_confirmed');
            return new RedirectResponse($url);
        }

        return false;
    }
}
