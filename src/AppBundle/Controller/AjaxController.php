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
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\HttpFoundation\JsonResponse;
	use AppBundle\Entity\Prize;

	/**
	 * Class AjaxController
	 * @package AppBundle\Controller
	 * @Route("/ajax")
	 */
	class AjaxController extends Controller
	{
		/**
		 * @Route("/stats", name="ajax_user_stats")
		 */
		public function userStatsAction(Request $request)
		{
			return new JsonResponse([]);
		}

		//, methods={"POST"}

		/**
		 * @Route("/winwheel/get_segments/", name="ajax_winwheel_get_segments")
		 */
		public function winwheelGetSegmentsAction(Request $request)
		{
			$user = $this->get('security.token_storage')->getToken()->getUser();

			$segments = $this->get('session')->get('segments');
			$segmentsGeneratedAt = $this->get('session')->get('segments_generated_at');

			// Re-generate each minute
			if(empty($segments) || empty($segmentsGeneratedAt) || (strtotime($segmentsGeneratedAt) - mktime() > 60))
			{
				// Regenerate wheel segments
				$segments = [];
				$segmentsCount = $this->container->getParameter('winwheel')['segments_count'];
				$colors = ['#eae56f', '#89f26e', '#7de6ef', '#e7706f', '#8CFF63', '#DD8EFF'];

				$repository = $this->getDoctrine()->getRepository(Prize::class);
				$query = $repository->createQueryBuilder('p')
					->where('p.quantity > :quantity')
					->setParameter('quantity', '0')
					->orderBy('p.quantity', 'ASC')
					->getQuery();
				$prizes = $query->getResult();

				// Fill with prizes
				foreach($prizes as $prize)
				{
					//print_r(json_encode($prize));
					$segments[] = [
						'id' => uniqid(),
						'text' => $prize->getTitle(),
						'fillStyle' => $colors[round(rand(0, count($colors)-1))],
						'description' => $prize->getDescription(),
						'image' => $prize->getImage(),
						'cost' => $prize->getCost(),
						'money' => null,
						'bonus' => null
					];
				}
				$segmentsCount -= count($segments);

				// Fill with scores bonuses
				$bonusCount = ceil($segmentsCount/2);
				for($i=1; $i<=$bonusCount; $i++)
				{
					$bonusSize = round(
						rand(
							$this->container->getParameter('winwheel')['min_loyalty_bonus'],
							$this->container->getParameter('winwheel')['max_loyalty_bonus']
						)
					);
					$segments[] = [
						'id' => uniqid(),
						'text' => (string)$bonusSize,
						'fillStyle' => $colors[round(rand(0, count($colors)-1))],
						'description' => null,
						'image' => null,
						'cost' => null,
						'money' => null,
						'bonus' => $bonusSize
					];
				}

				// Fill other segments with cash prizes
				$segmentsCount -= $bonusCount;
				for($i=1; $i<=$segmentsCount; $i++)
				{
					$bonusSize = round(
						rand(
							$this->container->getParameter('winwheel')['min_cash_bonus'],
							$this->container->getParameter('winwheel')['max_cash_bonus']
						)
					);
					$segments[] = [
						'id' => uniqid(),
						'text' => '$'.$bonusSize,
						'fillStyle' => $colors[round(rand(0, count($colors)-1))],
						'description' => null,
						'image' => null,
						'cost' => null,
						'money' => $bonusSize,
						'bonus' => null
					];
				}

				shuffle($segments);
				shuffle($segments);
				$segmentsGeneratedAt = date("r");

				$this->get('session')->set('segments', $segments);
				$this->get('session')->set('segments_generated_at', $segmentsGeneratedAt);
			}

			return new JsonResponse(['segments' => $segments, 'generated_at' => $segmentsGeneratedAt]);
			//return new Response("");
		}
	}