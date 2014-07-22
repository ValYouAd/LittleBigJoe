<?PHP
namespace LittleBigJoe\Bundle\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class RegistrationBetaController extends Controller
{
    /**
     * Tell the user his account is now confirmed
     *
     * @Route("/beta", name="littlebigjoe_frontendbundle_registration_beta_confirmed")
     * @Template()
     */
    public function confirmedAction()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();

        return $this->container->get('templating')->renderResponse('LittleBigJoeFrontendBundle:RegistrationBeta:confirmed.html.twig', array(
            'user' => $user,
        ));
    }
}