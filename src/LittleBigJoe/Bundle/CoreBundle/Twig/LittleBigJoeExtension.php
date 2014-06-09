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
        	return $translator->transchoice('%years% year|%years% years', $interval->format('%y'), array('%years%' => $interval->format('%y')));
        else if ($interval->m >= 1)
            return $translator->transchoice('%months% month|%months% months', $interval->format('%m'), array('%months%' => $interval->format('%m')));
        else if ($interval->days >= 1)
            return $translator->transchoice('%days% day|%days% days', $interval->format('%d'), array('%days%' => $interval->format('%d')));
        else if ($interval->days == 0 && $interval->h >= 1)
            return $translator->transchoice('%hours% hour|%hours% hours', $interval->format('%h'), array('%hours%' => $interval->format('%h')));
        else
            return $translator->transchoice('%minutes% minutes|%minutes% minutes', $interval->format('%i'), array('%minutes%' => $interval->format('%i')));
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