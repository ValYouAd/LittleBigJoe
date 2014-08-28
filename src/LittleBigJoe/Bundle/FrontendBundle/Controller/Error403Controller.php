<?php

namespace LittleBigJoe\Bundle\FrontendBundle\Controller;
use LittleBigJoe\Bundle\FrontendBundle\TwigExceptionEvents;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use LittleBigJoe\Bundle\CoreBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\Form\FormError;


class Error403Controller extends Controller
{
    public function editCodeBetaAction(Request $request) {
        $token = $this->get('security.context')->getToken();
        $user = $this->getUser();
        $is_beta_user = (in_array('ROLE_BETA_USER', $user->getRoles()) != false) ? true : false ;

        $form = $this->createFormBuilder($user)
                ->add('betaCodeValue', 'text', array('required' => false))
                ->add('submitCode', 'submit', array('label' => ('Submit')))
                ->getForm();

        $form->handleRequest($request);

        if ('POST' === $request->getMethod()) {
            $betaCodeValue = $user->getBetaCodeValue();
            if ($form->isValid()) {
                if (!empty($betaCodeValue)) {
                    $betaCode = $this->container->get('doctrine')->getRepository('LittleBigJoeCoreBundle:Code')->findOneByCode($betaCodeValue);
                    if ($betaCode) {
                        if ($betaCode->getMaxUse() <= $betaCode->getUsed() && $betaCode->getMaxUse() != 0) {
                            $form->get('betaCodeValue')->addError(new FormError(($this->get('translator')->trans('betacode.used_too_many_times'))));
                        } else {
                            $user->setBetaCode($betaCode);
                            $user->setRoles(array('ROLE_BETA_USER'));
                            $betaCode->setUsed($betaCode->getUsed() + 1);
                        }
                    } else {
                        $form->get('betaCodeValue')->addError(new FormError(($this->get('translator')->trans('betacode.incorrect'))));
                    }
                    $errors = $form->get('betaCodeValue')->getErrors();

                    if (empty($errors)) {
                        $em = $this->getDoctrine()->getManager();
                        $em->persist($user);
                        $em->flush();
                        $token->setAuthenticated(false);

                        return $this->render('LittleBigJoeFrontendBundle:Error403:error403.html.twig', array(
                            'form' => $form->createView(),
                            'is_beta_user' => true,
                            'user' => $user,
                        ));
                    }
                }
            }
        }

        return $this->render('LittleBigJoeFrontendBundle:Error403:error403.html.twig', array(
            'form' => $form->createView(),
            'is_beta_user' => $is_beta_user,
            'user' => $user,
        ));
    }
}