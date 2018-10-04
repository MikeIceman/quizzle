<?php
	/**
	 * Created by PhpStorm.
	 * User: Iceman
	 * Date: 23.09.2018
	 * Time: 16:03
	 */

	namespace AppBundle\Controller;

	use Symfony\Component\HttpFoundation\JsonResponse;
	use Symfony\Component\Routing\Annotation\Route;
	use Symfony\Bundle\FrameworkBundle\Controller\Controller;
	use Symfony\Component\HttpFoundation\Request;
	use AppBundle\Entity as Entity;
	use AppBundle\Entity\Operation;
	use AppBundle\Entity\UserPrize;
	use AppBundle\Service\Banking;

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

		#region Operations

		/**
		 * @Route("/operations/", name="admin_operations")
		 */
		public function operationsAction(Request $request)
		{
			$repository = $this->getDoctrine()->getRepository(Operation::class);

			// Get latest spin
			$operations = $repository->findBy(
				[
					'type' => ['bonus', 'win', 'exchange']
				],
				['id' => 'DESC']
			);

			return $this->render('dashboard/operations.html.twig', [
				'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
				'operations' => $operations,
				'conversion_rate' => $this->container->getParameter('loyalty_to_cash_rate')
			]);
		}

		/**
		 * @Route("/operations/update", name="admin_operation_update", methods={"GET", "POST"})
		 */
		public function operationUpdateAction(Request $request)
		{
			$user = $this->get('security.token_storage')->getToken()->getUser();

			$em = $this->getDoctrine()->getManager();
			$repository = $this->getDoctrine()->getRepository(Operation::class);

			$id = $request->get('id');
			$action = $request->get('action');

			if($id && $action)
			{
				$operation = $repository->find($id);

				if(!$operation)
					return new JsonResponse(['success' => false, 'error' => 404, 'message' => 'Not found']);

				$operationUser = $operation->getUser();

				if($action == 'accept' && $operation->getStatus() != 'complete')
				{
					switch ($operation->getType())
					{
						case 'win':
							$operationUser->updateBalance($operation->getAmount());
							break;
						case 'bonus':
							$operationUser->updateBonusBalance($operation->getAmount());
							break;
						case 'exchange':
							// Impossible situation: Pending Exchange operation. But who knows what may happen...
							$operationUser->updateBalance(-1*$operation->getAmount());
							$operationUser->updateBonusBalance($operation->getAmount() / $this->container->getParameter('loyalty_to_cash_rate'));
							break;
					}
					$operation->setStatus('complete');
					$operation->setUpdatedBy($user);
					$operation->setDateUpdated(new \DateTime());
					$em->persist($operation);
					$em->persist($operationUser);
				}
				elseif($action == 'reverse' && ($operation->getStatus() == 'complete' || $operation->getStatus() == 'pending'))
				{
					// Reverse operation
					if($operation->getStatus() == 'complete')
					{
						// Reverse balances only if completed
						switch ($operation->getType())
						{
							case 'win':
								$operationUser->updateBalance(-1 * $operation->getAmount());
								break;
							case 'bonus':
								$operationUser->updateBonusBalance(-1 * $operation->getAmount());
								break;
							case 'exchange':
								$operationUser->updateBalance($operation->getAmount());
								$operationUser->updateBonusBalance(-1 * $operation->getAmount() / $this->container->getParameter('loyalty_to_cash_rate'));
								break;
						}
					}
					$operation->setStatus('reversed');
					$operation->setUpdatedBy($user);
					$operation->setDateUpdated(new \DateTime());
					$em->persist($operationUser);
					$em->persist($operation);
				}
				else
				{
					return new JsonResponse(['success' => false, 'error' => 400, 'message' => 'Bad request']);
				}
				// Save changes
				$em->flush();

				return new JsonResponse([
					'success' => true,
					'error' => 0,
					'message' => 'Successfull update!',
					'data' => [
						'newStatus' => $operation->getStatus(),
						'updatedBy' => $user->getUsername(),
						'dateUpdated' => date('d.m.Y H:i:s')
					]
				]);
			}

			return new JsonResponse(['success' => false, 'error' => 400, 'message' => 'Bad request']);
		}

		#endregion

		#region Withdrawals
		/**
		 * @Route("/withdrawals/", name="admin_withdrawals")
		 */
		public function withdrawalsAction(Request $request)
		{
			$repository = $this->getDoctrine()->getRepository(Operation::class);

			// Get latest spin
			$withdrawals = $repository->findBy(['type' => 'withdrawal'], ['id' => 'DESC']);

			return $this->render('dashboard/withdrawals.html.twig', [
				'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
				'withdrawals' => $withdrawals,
			]);
		}

		/**
		 * @Route("/withdrawals/update", name="admin_withdrawal_update", methods={"GET", "POST"})
		 */
		public function withdrawalUpdateAction(Request $request)
		{
			$user = $this->get('security.token_storage')->getToken()->getUser();

			$banking = $this->get(Banking::class);

			$em = $this->getDoctrine()->getManager();
			$repository = $this->getDoctrine()->getRepository(Operation::class);

			$id = $request->get('id');
			$action = $request->get('action');

			if ($id && $action) {
				$operation = $repository->find($id);

				if (!$operation)
					return new JsonResponse(['success' => false, 'error' => 404, 'message' => 'Not found']);

				$operationUser = $operation->getUser();

				if($action == 'approve' && $operation->getStatus() == 'pending')
				{
					// Send txn and update entity
					try
					{
						$response = $banking->sendWithdrawalRequest($operation);
						$operation->setTxnId($response->getBatchHeader()->getPayoutBatchId());
						$operation->setStatus('complete');
					}
					catch (\Exception $ex)
					{
						return new JsonResponse(['success' => false, 'error' => 500, 'message' => 'Internal error']);
					}
				}
				elseif($action == 'cancel' && $operation->getStatus() == 'pending')
				{
					$operation->setStatus('cancelled');
					$operationUser->updateBalance($operation->getAmount());
				}
				elseif($action == 'refund' && $operation->getStatus() == 'complete')
				{
					$operation->setStatus('reversed');
					$operationUser->updateBalance($operation->getAmount());
				}
				else
				{
					return new JsonResponse(['success' => false, 'error' => 400, 'message' => 'Bad request']);
				}
				$operation->setUpdatedBy($user);
				$operation->setDateUpdated(new \DateTime());
				$em->persist($operationUser);
				$em->persist($operation);

				// Save changes
				$em->flush();

				return new JsonResponse([
					'success' => true,
					'error' => 0,
					'message' => 'Successfull update!',
					'data' => [
						'newStatus' => $operation->getStatus(),
						'updatedBy' => $user->getUsername(),
						'dateUpdated' => date('d.m.Y H:i:s'),
						'txnId' => $operation->getTxnId()
					]
				]);
			}
		}



		#endregion

		#region Winnings

		/**
		 * @Route("/winnings/", name="admin_winnings")
		 */
		public function winningsAction(Request $request)
		{
			$repository = $this->getDoctrine()->getRepository(UserPrize::class);

			// Get latest spin
			$prizes = $repository->findBy(
				[],
				['id' => 'DESC']
			);

			return $this->render('dashboard/winnings.html.twig', [
				'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
				'prizes' => $prizes
			]);
		}

		/**
		 * @Route("/winnings/update", name="admin_winnings_update", methods={"GET", "POST"})
		 */
		public function winningUpdateAction(Request $request)
		{
			$user = $this->get('security.token_storage')->getToken()->getUser();

			$em = $this->getDoctrine()->getManager();
			$repository = $this->getDoctrine()->getRepository(UserPrize::class);

			$id = $request->get('id');
			$action = $request->get('action');

			if($id && $action)
			{
				$prize = $repository->find($id);

				if(!$prize)
					return new JsonResponse(['success' => false, 'error' => 404, 'message' => 'Not found']);

				if($action == 'send' && $prize->getStatus() != 'sent' && $prize->getStatus() != 'received')
				{
					$prize->setStatus('sent');
				}
				elseif($action == 'reject' && $prize->getStatus() != 'complete')
				{
					// How can we reverse operation if order already received?
					$prize->setStatus('rejected');
				}
				elseif($action == 'delivered' && $prize->getStatus() == 'sent')
				{
					// Only sent can be delivered
					$prize->setStatus('received');
				}
				elseif($action == 'return' && $prize->getStatus() == 'sent')
				{
					// Only sent can be delivered
					$prize->setStatus('pending');
				}
				else
				{
					return new JsonResponse(['success' => false, 'error' => 400, 'message' => 'Bad request']);
				}

				$prize->setUpdatedBy($user);
				$prize->setDateUpdated(new \DateTime());
				$em->persist($prize);

				// TODO: Probably user email notification

				// Save changes
				$em->flush();

				return new JsonResponse([
					'success' => true,
					'error' => 0,
					'message' => 'Successfull update!',
					'data' => [
						'newStatus' => $prize->getStatus(),
						'updatedBy' => $user->getUsername(),
						'dateUpdated' => date('d.m.Y H:i:s')
					]
				]);
			}

			return new JsonResponse(['success' => false, 'error' => 400, 'message' => 'Bad request']);
		}
		#endregion
	}