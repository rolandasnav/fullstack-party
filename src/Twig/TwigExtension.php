<?php

namespace App\Twig;


use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TwigExtension extends AbstractExtension
{
    public static $units = [
        'y' => 'year',
        'm' => 'month',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second'
    ];

    /**
     * @return array|\Twig_Filter[]
     */
    public function getFilters()
    {
        return array(
            new TwigFilter('age', array($this, 'ageFilter')),
        );
    }

    /**
     * @param \DateTime $date
     * @return string
     * @throws \Exception
     */
    public function ageFilter(\DateTime $date): string
    {
        if ($date instanceof \DateTime) {
            $diff = $date->diff(new \DateTime('now'));

            foreach (self::$units as $attribute => $unit) {
                $count = $diff->$attribute;

                if ($count) {
                    return sprintf('%s %s ago', $count, ($count === 1) ? $unit : $unit . 's');
                }
            }
        }

        throw new \Exception('Invalid date provided');
    }
}