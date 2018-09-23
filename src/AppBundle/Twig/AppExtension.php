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
			);
		}

		public function md5Filter($string)
		{
			return md5($string);
		}
	}