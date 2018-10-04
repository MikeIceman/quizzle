<?php
	/**
	 * Created by PhpStorm.
	 * User: Iceman
	 * Date: 23.09.2018
	 * Time: 16:03
	 */

	namespace AppBundle\Controller;

	use Symfony\Component\Routing\Annotation\Route;
	use Symfony\Bundle\FrameworkBundle\Controller\Controller;
	use Symfony\Component\HttpFoundation\Request;
	use AppBundle\Entity as Entity;

	/**
	 * Class AdminController
	 * @package AppBundle\Controller
	 * @Route("/admin")
	 */
	class AdminController extends Controller
	{
		/**
		 * @Route("/", name="dashboard")
		 */
		public function indexAction(Request $request)
		{
			return $this->render('dashboard/index.html.twig', [
				'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
			]);
		}

		/**
		 * @Route("/operations/", name="admin_operations")
		 */
		public function operationsAction(Request $request)
		{
			return $this->render('dashboard/operations.html.twig', [
				'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
			]);
		}

		/**
		 * @Route("/withdrawals/", name="admin_withdrawals")
		 */
		public function withdrawalsAction(Request $request)
		{
			return $this->render('dashboard/withdrawals.html.twig', [
				'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
			]);
		}
	}