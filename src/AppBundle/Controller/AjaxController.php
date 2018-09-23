<?php
	/**
	 * Created by PhpStorm.
	 * User: Iceman
	 * Date: 21.09.2018
	 * Time: 19:29
	 */

	namespace AppBundle\Controller;

	use Symfony\Component\Routing\Annotation\Route;
	use Symfony\Bundle\FrameworkBundle\Controller\Controller;
	use Symfony\Component\HttpFoundation\Request;

	/**
	 * Class AjaxController
	 * @package AppBundle\Controller
	 * @Route("/ajax")
	 */
	class AjaxController extends Controller
	{
		/**
		 * @Route("/stats", name="ajax_shops_stats")
		 */
		public function shopsStatsAction(Request $request)
		{

		}
	}