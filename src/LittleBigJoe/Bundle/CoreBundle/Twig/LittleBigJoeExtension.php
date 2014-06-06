<?php

namespace LittleBigJoe\Bundle\CoreBundle\Twig;

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
        		new \Twig_SimpleFilter('country', array($this, 'countryFilter')),
        );
    }

    /**
     * Get time remaining
     */
    public function getTimeRemaining($endingDate)
    {
        $nowDate = new \DateTime();
        $interval = $endingDate->diff($nowDate);
        $translator = $this->container->get('translator');

        if ($interval->y >= 1)
        	return $translator->trans('%years% year(s)', array('%years%' => $interval->format('%y')));
        else if ($interval->m >= 1)
            return $translator->trans('%months% month(s)', array('%months%' => $interval->format('%m')));
        else if ($interval->days >= 1)
            return $translator->trans('%days% day(s)', array('%days%' => $interval->format('%d')));
        else if ($interval->days == 0 && $interval->h >= 1)
            return $translator->trans('%hours% hour(s)', array('%hours%' => $interval->format('%h')));
        else
            return $translator->trans('%minutes% minutes(s)', array('%minutes%' => $interval->format('%i')));
    }

    /** 
     * Get country full name
     */
    public function countryFilter($countryCode, $locale = "en")
    {
	    	$c = \Symfony\Component\Locale\Locale::getDisplayCountries($locale);
	    
	    	return array_key_exists($countryCode, $c)
			    	? $c[$countryCode]
			    	: $countryCode;
    }    
    
    public function getName()
    {
        return 'littlebigjoe_extension';
    }
}