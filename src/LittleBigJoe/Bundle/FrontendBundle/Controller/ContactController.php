<?php

namespace LittleBigJoe\Bundle\FrontendBundle\Controller;

use LittleBigJoe\Bundle\FrontendBundle\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class ContactController extends Controller
{
    /**
     * Contact form page
     *
     * @Route("/contact", name="littlebigjoe_frontendbundle_contact")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $form = $this->createForm(new ContactType());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $message = \Swift_Message::newInstance()
                ->setContentType('text/html')
                ->setSubject($this->container->get('translator')->trans('Contact request'))
                ->setFrom($this->container->getParameter('default_email_address'))
                ->setTo($this->container->getParameter('default_email_address'))
                ->setBody(
                    $this->renderView(
                        'LittleBigJoeFrontendBundle:Email:contact.html.twig',
                        array(
                            'ip' => $request->getClientIp(),
                            'name' => $form->get('name')->getData(),
                            'email' => $form->get('email')->getData(),
                            'message' => $form->get('message')->getData(),
                        )
                    )
                );

            $this->get('mailer')->send($message);

            $request->getSession()->getFlashBag()->add('success', $this->get('translator')->trans('Your email has been sent! Thanks!'));

            return $this->redirect($this->generateUrl('littlebigjoe_frontendbundle_contact'));
        }

        return array(
            'form' => $form->createView()
        );
    }
}
