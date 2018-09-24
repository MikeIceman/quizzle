<?php
	/**
	 * Created by PhpStorm.
	 * User: Iceman
	 * Date: 23.09.2018
	 * Time: 21:41
	 */

	namespace AppBundle\Twig;

	use Twig\Extension\AbstractExtension;
	use Twig\TwigFilter;

	class AppExtension extends AbstractExtension
	{
		public function getFilters()
		{
			return array(
				new TwigFilter('md5', array($this, 'md5Filter')),
				new TwigFilter('money', array($this, 'moneyFilter')),
				new TwigFilter('number', array($this, 'numberFilter')),
			);
		}

		public function md5Filter($string)
		{
			return md5($string);
		}

		public function moneyFilter($number, $decimals = 0, $decPoint = '.', $thousandsSep = ',')
		{
			$price = number_format($number, $decimals, $decPoint, $thousandsSep);
			$price = '$'.$price;

			return $price;
		}

		public function numberFilter($number, $decimals = 0, $decPoint = '.', $thousandsSep = ',')
		{
			return number_format($number, $decimals, $decPoint, $thousandsSep);
		}
	}