<?php

namespace AppBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Class AppExtension
 *
 * @package AppBundle\Twig
 */
class AppExtension extends AbstractExtension
{
    /**
     * @return array|TwigFilter[]
     */
    public function getFilters()
    {
        return [
            new TwigFilter('md5', [$this, 'md5Filter']),
            new TwigFilter('money', [$this, 'moneyFilter']),
            new TwigFilter('number', [$this, 'numberFilter']),
        ];
    }

    /**
     * @param $string
     *
     * @return string
     */
    public function md5Filter($string)
    {
        return md5($string);
    }

    /**
     * @param $number
     * @param int $decimals
     * @param string $decPoint
     * @param string $thousandsSep
     *
     * @return string
     */
    public function moneyFilter($number, $decimals = 0, $decPoint = '.', $thousandsSep = ',')
    {
        $price = number_format($number, $decimals, $decPoint, $thousandsSep);
        return '$' . $price;
    }

    /**
     * @param $number
     * @param int $decimals
     * @param string $decPoint
     * @param string $thousandsSep
     *
     * @return string
     */
    public function numberFilter($number, $decimals = 0, $decPoint = '.', $thousandsSep = ',')
    {
        return number_format($number, $decimals, $decPoint, $thousandsSep);
    }
}
