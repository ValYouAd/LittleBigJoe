<?php

namespace LittleBigJoe\Bundle\FrontendBundle\Controller;

use Symfony\Bundle\TwigBundle\Controller\ExceptionController as BaseController;
use Symfony\Bundle\FrameworkBundle\Templating\TemplateReference;
use Symfony\Component\HttpKernel\Exception\FlattenException;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;


class TwigExceptionController extends BaseController {

    protected $container;

    public function setContainer($container)
    {
        $this->container = $container;
    }

    /**
     * Converts an Exception to a Response.
     *
     * @param Request              $request   The request
     * @param FlattenException     $exception A FlattenException instance
     * @param DebugLoggerInterface $logger    A DebugLoggerInterface instance
     * @param string               $_format   The format to use for rendering (html, xml, ...)
     *
     * @return Response
     *
     * @throws \InvalidArgumentException When the exception template does not exist
     */
    public function showAction(Request $request, FlattenException $exception, DebugLoggerInterface $logger = null, $_format = 'html')
    {
        $currentContent = $this->getAndCleanOutputBuffering($request->headers->get('X-Php-Ob-Level', -1));

        $code = $exception->getStatusCode();

//        return $this->redirect($this->generateUrl('littlebigjoe_frontendbundle_registration_beta_confirmed'));

//        return new RedirectResponse($this->generateUrl('littlebigjoe_frontendbundle_registration_beta_confirmed'));

//        $url = $this->container->get('router')->generate('main_homepage');
//        $response = new \Symfony\Component\HttpFoundation\RedirectResponse($url);
//        return $response;

        return new Response($this->twig->render(
            $this->findTemplate($request, $_format, $code, $this->debug),
            array(
                'status_code'    => $code,
                'status_text'    => isset(Response::$statusTexts[$code]) ? Response::$statusTexts[$code] : '',
                'exception'      => $exception,
                'logger'         => $logger,
                'currentContent' => $currentContent,
            )
        ));
    }

    /**
     * @param Request $request
     * @param string  $format
     * @param int     $code       An HTTP response status code
     * @param bool    $debug
     *
     * @return TemplateReference
     */
    protected function findTemplate(Request $request, $format, $code, $debug)
    {
        $name = $debug ? 'exception' : 'error';
        if ($debug && 'html' == $format) {
            $name = 'exception_full';
        }

        // when not in debug, try to find a template for the specific HTTP status code and format
        if (!$debug) {
            if ($code == 403)
                return 'LittleBigJoeFrontendBundle:RegistrationBeta:confirmed.html.twig';
            $template = new TemplateReference('TwigBundle', 'Exception', $name.$code, $format, 'twig');
            if ($this->templateExists($template)) {
                return $template;
            }
        }

        // try to find a template for the given format
        $template = new TemplateReference('TwigBundle', 'Exception', $name, $format, 'twig');
        if ($this->templateExists($template)) {
            return $template;
        }

        // default to a generic HTML exception
        $request->setRequestFormat('html');

        return new TemplateReference('TwigBundle', 'Exception', $name, 'html', 'twig');
    }
}