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
	use AppBundle\Entity\WinWheelSpin;
	use AppBundle\Entity\UserPrize;
	use AppBundle\Entity\Operation;


	/**
	 * Class PersonalController
	 * @package AppBundle\Controller
	 * @Route("/personal")
	 */
	class PersonalController extends Controller
	{
		/**
		 * @Route("/", name="personal_index")
		 */
		public function indexAction(Request $request)
		{
			// Profile
			$user = $this->get('security.token_storage')->getToken()->getUser();

			$role = $user->getRoleDescription($user->getHighestRole());

			return $this->render('personal/index.html.twig', array(
				'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
				'user' => $user,
				'role' => $role
			));
		}

		/**
		 * @Route("/winnings/", name="personal_winnings")
		 */
		public function winningsAction(Request $request)
		{
			$user = $this->get('security.token_storage')->getToken()->getUser();

			$repository = $this->getDoctrine()->getRepository(WinWheelSpin::class);

			// Get latest spin
			$winnings = $repository->findBy(
				[
					'user' => $user,
					'prizeType' => ['cash', 'bonus'],
				],
				['id' => 'DESC']
			);

			return $this->render('personal/winnings.html.twig', [
				'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
				'winnings' => $winnings
			]);
		}

		/**
		 * @Route("/prizes/", name="personal_prizes")
		 */
		public function prizesAction(Request $request)
		{
			$user = $this->get('security.token_storage')->getToken()->getUser();

			$repository = $this->getDoctrine()->getRepository(UserPrize::class);

			// Get latest spin
			$prizes = $repository->findBy(
				[
					'user' => $user,
				],
				['id' => 'ASC']
			);

			return $this->render('personal/prizes.html.twig', [
				'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
				'prizes' => $prizes
			]);
		}

		/**
		 * @Route("/operations/", name="personal_operations")
		 */
		public function operationsAction(Request $request)
		{
			$user = $this->get('security.token_storage')->getToken()->getUser();

			$repository = $this->getDoctrine()->getRepository(Operation::class);

			// Get latest spin
			$operations = $repository->findBy(
				[
					'user' => $user,
				],
				['id' => 'ASC']
			);

			return $this->render('personal/operations.html.twig', [
				'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
				'operations' => $operations,
			]);
		}

		/**
		 * @Route("/withdrawals/", name="personal_withdrawals")
		 */
		public function withdrawalsAction(Request $request)
		{
			return $this->render('personal/withdrawals.html.twig', [
				'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
			]);
		}
	}