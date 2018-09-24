<?php
	/**
	 * Created by PhpStorm.
	 * User: Iceman
	 * Date: 21.09.2018
	 * Time: 19:22
	 */

	namespace AppBundle\Controller;

	use Symfony\Component\Routing\Annotation\Route;
	use Symfony\Bundle\FrameworkBundle\Controller\Controller;
	use Symfony\Component\HttpFoundation\Request;


	/**
	 * Class PersonalController
	 * @package AppBundle\Controller
	 * @Route("/personal")
	 */
	class PersonalController extends Controller
	{
		/**
		 * @Route("/", name="personal")
		 */
		public function indexAction(Request $request)
		{
			return $this->render('personal/index.html.twig', [
				'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
			]);
		}
	}