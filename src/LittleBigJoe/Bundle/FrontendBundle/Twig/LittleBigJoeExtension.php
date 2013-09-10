<?php

namespace LittleBigJoe\Bundle\FrontendBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Twig controller.
 *
 */
class LittleBigJoeExtension extends \Twig_Extension
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFilters()
    {
        return array(
            'time_remaining' => new \Twig_Filter_Method($this, 'getTimeRemaining'),
        );
    }

    public function getTimeRemaining($endingDate)
    {
        $nowDate = new \DateTime();
        $interval = $endingDate->diff($nowDate);

        if ($interval->days >= 1)
            return $interval->format('%d day(s) remaining');
        else if ($interval->days == 0 && $interval->h >= 1)
            return $interval->format('%h hour(s) remaining');
        else
            return $interval->format('%i minute(s) remaining');
    }

    public function getName()
    {
        return 'littlebigjoe_extension';
    }
}