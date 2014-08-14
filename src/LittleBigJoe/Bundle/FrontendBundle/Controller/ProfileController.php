<?php

namespace LittleBigJoe\Bundle\FrontendBundle\Controller;

use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use LittleBigJoe\Bundle\CoreBundle\Entity\User;
use FOS\UserBundle\Event\FormEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\Controller\ProfileController as BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ProfileController extends BaseController
{
    /**
     * Edit the user
     */
    public function editAction(Request $request)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof User) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        // Store old photo for backup
        $oldPhoto = $user->getPhoto();

        $dispatcher = $this->container->get('event_dispatcher');

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $formFactory = $this->container->get('fos_user.profile.form.factory');
        $formFactoryPassword = $this->container->get('fos_user.change_password.form.factory');

        $form = $formFactory->createForm();
        $form->setData($user);

        $formPassword = $formFactoryPassword->createForm();
        $formPassword->setData($user);

        if ('POST' === $request->getMethod()) {
            if ($request->request->has('fos_user_profile_form')) {
                $form->bind($request);

                if ($form->isValid())
                {
                    $userManager = $this->container->get('fos_user.user_manager');

                    $event = new FormEvent($form, $request);
                    $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_SUCCESS, $event);

                    if ($user->getPhoto() == null)
                    {
                        $user->setPhoto($oldPhoto);
                    }

                    $userManager->updateUser($user);

                    if (null === $response = $event->getResponse()) {
                        $url = $this->container->get('router')->generate('fos_user_profile_show');
                        $response = new RedirectResponse($url);
                    }

                    $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

                    return $response;
                }
            }
            else if($request->request->has('fos_user_change_password_form')){
                $formPassword->bind($request);

                $encoder_service = $this->container->get('security.encoder_factory');
                $encoder = $encoder_service->getEncoder($user);
                $encoded_pass = $encoder->encodePassword($formPassword->get('current_password')->getData(), $user->getSalt());

                if ($formPassword->isValid() && $user->getPassword() == $encoded_pass) {
                    /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
                    $userManager = $this->container->get('fos_user.user_manager');

                    $event = new FormEvent($formPassword, $request);
                    $dispatcher->dispatch(FOSUserEvents::CHANGE_PASSWORD_SUCCESS, $event);

                    $userManager->updateUser($user);

                    if (null === $response = $event->getResponse()) {
                        $url = $this->container->get('router')->generate('fos_user_profile_edit');
                        $response = new RedirectResponse($url);
                    }

                    $dispatcher->dispatch(FOSUserEvents::CHANGE_PASSWORD_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

                }
            }
        }

        return $this->container->get('templating')->renderResponse(
            'FOSUserBundle:Profile:edit.html.'.$this->container->getParameter('fos_user.template.engine'),
            array(
                'form' => $form->createView(),
                'formPassword' => $formPassword->createView(),
            )
        );
    }
}
