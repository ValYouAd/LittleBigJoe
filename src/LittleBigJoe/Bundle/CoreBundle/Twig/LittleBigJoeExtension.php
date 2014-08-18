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
    protected $translator;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->translator = $container->get('translator');
    }

    public function getFilters()
    {
        return array(
            'time_remaining' => new \Twig_Filter_Method($this, 'getTimeRemaining'),
        	new \Twig_SimpleFilter('country', array($this, 'countryFilter')),
            new \Twig_SimpleFilter('distance_of_time_in_words', array($this, 'distanceOfTimeInWordsFilter')),
            new \Twig_SimpleFilter('time_ago_in_words', array($this, 'timeAgoInWordsFilter')),
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

        /*if ($interval->y >= 1)
        	return $translator->transchoice('%years% year|%years% years', $interval->format('%y'), array('%years%' => $interval->format('%y')));
        else if ($interval->m >= 1)
            return $translator->transchoice('%months% month|%months% months', $interval->format('%m'), array('%months%' => $interval->format('%m')));
        else*/
        if ($interval->days >= 1)
            return $translator->transchoice('%days% day|%days% days', $interval->days, array('%days%' => $interval->days));
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

    /**
     * Like distance_of_time_in_words, but where to_time is fixed to timestamp()
     *
     * @param $from_time String or DateTime
     * @param bool $include_seconds
     * @param bool $include_months
     *
     * @return mixed
     */
    function timeAgoInWordsFilter($from_time, $include_seconds = false, $include_months = false)
    {
        return $this->distanceOfTimeInWordsFilter($from_time, new \DateTime('now'), $include_seconds, $include_months);
    }

    /**
     * Reports the approximate distance in time between two times given in seconds
     * or in a valid ISO string like.
     * For example, if the distance is 47 minutes, it'll return
     * "about 1 hour". See the source for the complete wording list.
     *
     * Integers are interpreted as seconds. So, by example to check the distance of time between
     * a created user an it's last login:
     * {{ user.createdAt|distance_of_time_in_words(user.lastLoginAt) }} returns "less than a minute".
     *
     * Set include_seconds to true if you want more detailed approximations if distance < 1 minute
     * Set include_months to true if you want approximations in months if days > 30
     *
     * @param $from_time String or DateTime
     * @param $to_time String or DateTime
     * @param bool $include_seconds
     * @param bool $include_months
     *
     * @return mixed
     */
    public function distanceOfTimeInWordsFilter($from_time, $to_time = null, $include_seconds = false, $include_months = false)
    {
        $datetime_transformer = new \Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer(null, null, 'Y-m-d H:i:s');
        $timestamp_transformer = new \Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToTimestampTransformer();

        # Transforming to Timestamp
        if (!($from_time instanceof \DateTime) && !is_numeric($from_time)) {
            $from_time = $datetime_transformer->reverseTransform($from_time);
            $from_time = $timestamp_transformer->transform($from_time);
        } elseif($from_time instanceof \DateTime) {
            $from_time = $timestamp_transformer->transform($from_time);
        }

        $to_time = empty($to_time) ? new \DateTime('now') : $to_time;

        # Transforming to Timestamp
        if (!($to_time instanceof \DateTime) && !is_numeric($to_time)) {
            $to_time = $datetime_transformer->reverseTransform($to_time);
            $to_time = $timestamp_transformer->transform($to_time);
        } elseif($to_time instanceof \DateTime) {
            $to_time = $timestamp_transformer->transform($to_time);
        }

        $distance_in_minutes = round((abs($to_time - $from_time))/60);
        $distance_in_seconds = round(abs($to_time - $from_time));

        if ($distance_in_minutes <= 1){
            if ($include_seconds){
                if ($distance_in_seconds < 5){
                    return $this->translator->trans('less than %seconds seconds ago', array('%seconds' => 5));
                }
                elseif($distance_in_seconds < 10){
                    return $this->translator->trans('less than %seconds seconds ago', array('%seconds' => 10));
                }
                elseif($distance_in_seconds < 20){
                    return $this->translator->trans('less than %seconds seconds ago', array('%seconds' => 20));
                }
                elseif($distance_in_seconds < 40){
                    return $this->translator->trans('half a minute ago');
                }
                elseif($distance_in_seconds < 60){
                    return $this->translator->trans('less than a minute ago');
                }
                else {
                    return $this->translator->trans('1 minute ago');
                }
            }
            return ($distance_in_minutes===0) ? $this->translator->trans('less than a minute ago', array()) : $this->translator->trans('1 minute ago', array());
        }
        elseif ($distance_in_minutes <= 45){
            return $this->translator->transchoice('%minutes minutes ago', $distance_in_minutes, array('%minutes' => $distance_in_minutes));
        }
        elseif ($distance_in_minutes <= 90){
            return $this->translator->trans('about 1 hour ago');
        }
        elseif ($distance_in_minutes <= 1440){
            return $this->translator->transchoice('about %hours hours ago', round($distance_in_minutes/60), array('%hours' => round($distance_in_minutes/60)));
        }
        elseif ($distance_in_minutes <= 2880){
            return $this->translator->trans('1 day ago');
        }
        else{
            $distance_in_days = round($distance_in_minutes/1440);
            if (!$include_months || $distance_in_days <= 30) {
                return $this->translator->trans('%days days ago', array('%days' => round($distance_in_days)));
            }
            else {
                return $this->translator->transchoice('{1} 1 month ago |]1,Inf[ %months months ago', round($distance_in_days/30), array('%months' => round($distance_in_days/30)));
            }
        }
    }
    
    public function getName()
    {
        return 'littlebigjoe_extension';
    }
}