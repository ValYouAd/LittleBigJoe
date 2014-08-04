<?php

namespace LittleBigJoe\Bundle\FrontendBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use LittleBigJoe\Bundle\CoreBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;

class Error403Controller extends Controller
{
    public function codeBetaAction(Request $request) {
        $user = $request->getUser();

        $form = $this->createFormBuilder($user)
                ->add('betaCodeValue', 'text', array('label' => ('Get your beta access now!'), 'required' => false))
                ->add('submitCode', 'submit', array('label' => ('Submit')))
                ->getForm();

        return $this->render('LittleBigJoeFrontendBundle:Error403:error403.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}