<?php

namespace LittleBigJoe\Bundle\FrontendBundle\EventListener;

use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\UserEvent;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\FOSUserBundle;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class UserListener implements EventSubscriberInterface
{
    /** @var \Symfony\Component\Security\Core\SecurityContext */
    private $securityContext;

    /**
     * Constructor
     *
     * @param SecurityContext $securityContext
     * @param Doctrine        $doctrine
     */
    public function __construct(SecurityContext $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::SECURITY_IMPLICIT_LOGIN => 'onSecurityInteractivelogin',
            FOSUserEvents::REGISTRATION_COMPLETED=> 'onRegistrationCompleted'
        );
    }

    public function onSecurityInteractivelogin(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();
        $request = $event->getRequest();

        if ($this->securityContext->isGranted('IS_AUTHENTICATED_FULLY')) {
            $request->setLocale($this->securityContext->getToken()->getUser()->getDefaultLanguage());
            $request->getSession()->set('_locale', $this->securityContext->getToken()->getUser()->getDefaultLanguage());
        }
    }

    public function onRegistrationCompleted(FilterUserResponseEvent $event){
        $user = $event->getAuthenticationToken()->getUser();
        $request = $event->getRequest();

        $request->setLocale($user->getDefaultLanguage());
        $request->getSession()->set('_locale', $user->getDefaultLanguage());
    }
}