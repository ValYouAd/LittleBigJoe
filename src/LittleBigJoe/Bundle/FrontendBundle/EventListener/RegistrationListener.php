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
use FOS\UserBundle\Event\GetResponseUserEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormError;


class RegistrationListener implements EventSubscriberInterface
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::REGISTRATION_SUCCESS => 'onRegistrationSuccess',
            FOSUserEvents::REGISTRATION_INITIALIZE => 'onRegistrationInitialize',
        );
    }

    public function onRegistrationInitialize(GetResponseUserEvent $event)
    {
        $formFactory = $this->container->get('fos_user.registration.form.factory');
        $user = $event->getUser();

        $form = $formFactory->createForm();
        $form->setData($user);
        $form->bind($event->getRequest());

        $betaCodeValue = $event->getUser()->getBetaCodeValue();
        if (!empty($betaCodeValue)) {
            $betaCode = $this->container->get('doctrine')->getRepository('LittleBigJoeCoreBundle:Code')->findOneByCode($betaCodeValue);
            if ($betaCode && ($betaCode->getMaxUse() >= $betaCode->getUsed() || $betaCode->getMaxUse() == 0)) {
                $user->setBetaCode($betaCode);
                $user->addRole('ROLE_BETA_USER');
            } else {
                $form->get('betaCodeValue')->addError(new FormError('Code incorrect'));
                $event->setResponse($this->container->get('templating')->renderResponse('FOSUserBundle:Registration:register.html.twig', array(
                    'form' => $form->createView(),
                )));
            }
        }
    }

    public function onRegistrationSuccess(FormEvent $event)
    {
        /** @var $user \FOS\UserBundle\Model\UserInterface */
        $user = $event->getForm()->getData();
        $em = $this->container->get('doctrine')->getManager();
        $userManager = $this->container->get('fos_user.user_manager');

        $betaCodeValue = $user->getBetaCodeValue();
        if (!empty($betaCodeValue)){
            $betaCode = $this->container->get('doctrine')->getRepository('LittleBigJoeCoreBundle:Code')->findOneByCode($betaCodeValue);
            if (!empty($betaCode)){
                $user->setBetaCode($betaCode);
//                $user->setRoles'ROLE_BETA_USER');
            }
        }

        // Upload user photo
        if ($user->getPhoto() != null) 
        {
            $evm = $em->getEventManager();
            $uploadableManager = $this->container->get('stof_doctrine_extensions.uploadable.manager');
            $evm->removeEventListener(array('postFlush'), $uploadableManager->getUploadableListener());
            $uploadableManager->markEntityToUpload($user, $user->getPhoto());
        }

        // Stock temporarly user plain password, to send it by email
        $plainPassword = $user->getPlainPassword();
        
        $userManager->updateUser($user);
        
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
        
        // Send welcome email
        $email = \Swift_Message::newInstance()
					        ->setContentType('text/html')
					        ->setSubject($this->container->get('translator')->trans('Welcome to Little Big Joe'))
					        ->setFrom($this->container->getParameter('default_email_address'))
					        ->setTo(array($user->getEmail() => $user))
					        ->setBody(
					        		$this->container->get('templating')->render('LittleBigJoeFrontendBundle:Email:welcome.html.twig', array(
					        				'user' => $user,
					        				'plainPassword' => $plainPassword,
					        				'url' => $this->container->get('request')->getSchemeAndHttpHost()
					        		), 'text/html')
					        );
        $this->container->get('mailer')->send($email);
        
				// Redirect user to confirmation page
        if (null === $response = $event->getResponse()) 
        {
            $url = $this->container->get('router')->generate('fos_user_registration_confirmed');
            return new RedirectResponse($url);
        }

        return false;
    }
}
