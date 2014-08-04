<?php

namespace LittleBigJoe\Bundle\FrontendBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use LittleBigJoe\Bundle\CoreBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;


class Error403Controller extends Controller
{
    public function editCodeBetaAction(Request $request) {
  /*      $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof User) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $dispatcher = $this->container->get('event_dispatcher');

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }*/

        $user = $request->getUser();

        $form = $this->createFormBuilder($user)
                ->add('betaCodeValue', 'text', array('label' => ('Get your beta access now!'), 'required' => false))
                ->add('submitCode', 'submit', array('label' => ('Submit')))
                ->getForm();

        $form->handleRequest($request);

        echo $request->getMethod();
        if ($form->isSubmitted()) echo "test";
        if ('POST' === $request->getMethod()) {
            if ($form->isValid()) {
//                echo "test";die;
//              return $this->redirect($this->generateUrl('task_success'));
            }
        }

        return $this->render('LittleBigJoeFrontendBundle:Error403:error403.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}