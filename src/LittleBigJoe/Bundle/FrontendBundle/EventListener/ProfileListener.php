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

class ProfileListener implements EventSubscriberInterface
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
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

        if (null === $response = $event->getResponse()) {
            $url = $this->container->get('router')->generate('fos_user_registration_confirmed');
            return new RedirectResponse($url);
        }

        return false;
    }
}
