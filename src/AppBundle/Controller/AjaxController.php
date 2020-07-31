<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Operation;
use AppBundle\Entity\UserPrize;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\Prize;
use AppBundle\Entity\WinWheelSpin;

/**
 * Class AjaxController
 * @package AppBundle\Controller
 * @Route("/ajax")
 */
class AjaxController extends Controller
{
    /**
     * @Route("/stats", name="ajax_user_stats", methods={"GET", "POST"})
     * @return JsonResponse
     */
    public function userStatsAction()
    {
        return new JsonResponse([]);
    }

    /**
     * @Route("/winwheel/get_segments/", name="ajax_winwheel_get_segments", methods={"GET", "POST"})
     * @return JsonResponse
     */
    public function winWheelGetSegmentsAction()
    {
        $segments = $this->get('session')->get('segments');
        $segmentsGeneratedAt = $this->get('session')->get('segments_generated_at');

        // Re-generate each minute (or another time interval from config)
        if (
            empty($segments) ||
            empty($segmentsGeneratedAt) ||
            (time() - strtotime($segmentsGeneratedAt)  > $this->container->getParameter('winwheel')['spin_delay'])
        ) {
            #region Re-generate wheel segments
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
            #endregion

            //NOTE: Add empty segments if needed and handle them as 'nothing' wheel spin result

            #region Fill segments with prizes
            foreach ($prizes as $prize) {
                $segments[] = [
                    'id' => uniqid('', true),
                    'text' => $prize->getTitle(),
                    'fillStyle' => $colors[round(mt_rand(0, count($colors) - 1))],
                    'description' => $prize->getDescription(),
                    'image' => $prize->getImage(),
                    'cost' => $prize->getCost(),
                    'prize_id' => $prize->getId(),
                    'money' => null,
                    'bonus' => null
                ];
            }
            $segmentsCount -= count($segments);
            #endregion

            #region Fill segments with scores bonuses
            $bonusCount = ceil($segmentsCount / 2);
            for ($i = 1; $i <= $bonusCount; $i++) {
                $bonusSize = round(
                    mt_rand(
                        $this->container->getParameter('winwheel')['min_loyalty_bonus'],
                        $this->container->getParameter('winwheel')['max_loyalty_bonus']
                    )
                );
                $segments[] = [
                    'id' => uniqid('', true),
                    'text' => (string)$bonusSize,
                    'fillStyle' => $colors[round(mt_rand(0, count($colors) - 1))],
                    'description' => null,
                    'image' => null,
                    'cost' => null,
                    'prize_id' => null,
                    'money' => null,
                    'bonus' => $bonusSize
                ];
            }
            #endregion

            #region Fill other segments with cash prizes
            $segmentsCount -= $bonusCount;
            for ($i = 1; $i <= $segmentsCount; $i++) {
                $bonusSize = round(
                    mt_rand(
                        $this->container->getParameter('winwheel')['min_cash_bonus'],
                        $this->container->getParameter('winwheel')['max_cash_bonus']
                    )
                );
                $segments[] = [
                    'id' => uniqid('', true),
                    'text' => '$' . $bonusSize,
                    'fillStyle' => $colors[round(mt_rand(0, count($colors) - 1))],
                    'description' => null,
                    'image' => null,
                    'cost' => null,
                    'prize_id' => null,
                    'money' => $bonusSize,
                    'bonus' => null
                ];
            }
            #endregion

            // Shuffle twice for more random
            shuffle($segments);
            shuffle($segments);
            $segmentsGeneratedAt = date("r");

            $this->get('session')->set('segments', $segments);
            $this->get('session')->set('segments_generated_at', $segmentsGeneratedAt);
            $this->get('session')->set('already_spinned', false);
        }

        $user = $this->get('security.token_storage')->getToken()->getUser();
        $repository = $this->getDoctrine()->getRepository(WinWheelSpin::class);
        $lastSpinDate = date(
            "r",
            strtotime('-' . $this->container->getParameter('winwheel')['spin_delay'] . ' seconds')
        );

        // Get latest spin
        $lastSpin = $repository->findOneBy(
            ['user' => $user]
        );

        if (!empty($lastSpin)) {
            $lastSpinDate = $lastSpin->getDateSpinned()->format("r");
        }

        $nextSpinDate = date(
            "r",
            strtotime(
                '+' . $this->container->getParameter('winwheel')['spin_delay'] . ' seconds',
                strtotime($lastSpinDate)
            )
        );

        return new JsonResponse(
            [
                'segments' => $segments,
                'generated_at' => $segmentsGeneratedAt,
                'lastSpin' => $lastSpinDate,
                'nextSpin' => $nextSpinDate
            ]
        );
    }

    /**
     * @Route("/winwheel/get_lucky_segment/", name="ajax_winwheel_get_lucky_segment", methods={"GET", "POST"})
     * @return JsonResponse|Response
     */
    public function winWheelGetLuckySegmentAction()
    {
        if ($this->get('session')->get('already_spinned')) {
            // Already spinned and not re-generated
            return new Response("ERROR");
        }

        $user = $this->get('security.token_storage')->getToken()->getUser();
        $segments = $this->get('session')->get('segments');

        $em = $this->getDoctrine()->getManager();
        $spin = new WinWheelSpin();
        $spin->setUser($user);

        $luckySegmentId = round(mt_rand(1, count($segments)));
        $luckySegment = $segments[$luckySegmentId - 1];

        if ($luckySegment['bonus'] > 0) {
            $spin->setPrizeType('bonus');
            $spin->setPrizeAmount($luckySegment['bonus']);
        } elseif ($luckySegment['money'] > 0) {
            $spin->setPrizeType('cash');
            $spin->setPrizeAmount($luckySegment['money']);
        } elseif ($luckySegment['cost'] > 0) {
            $repository = $this->getDoctrine()->getRepository(Prize::class);
            $prize = $repository->find($luckySegment['prize_id']);

            $spin->setPrizeType('prize');
            $spin->setPrizeAmount($luckySegment['cost']);
            $spin->setPrize($prize);
        } else {
            $spin->setPrizeType('nothing');
        }

        $em->persist($spin);
        $em->flush();

        $this->get('session')->set('already_spinned', true);

        return new JsonResponse([
            'segment' => $luckySegmentId,
            'nextSpin' => date(
                "r",
                strtotime('+' . $this->container->getParameter('winwheel')['spin_delay'] . ' seconds')
            ),
            'spinId' => $spin->getId()]);
    }

    /**
     * @Route("/winwheel/accept/", name="ajax_winwheel_accept_winnings", methods={"GET", "POST"})
     */
    public function winWheelAcceptWinningsAction(Request $request)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $id = $request->get('spinId');

        $em = $this->getDoctrine()->getManager();

        $spin = $em->getRepository('AppBundle:WinWheelSpin')->findOneBy(
            [
                'id' => $id,
                'user' => $user,
                'status' => 'pending'
            ]
        );

        if ($spin) {
            $spin->setStatus('accepted');
            $em->persist($spin);

            switch ($spin->getPrizeType()) {
                case 'bonus':
                    // Update user bonus balance
                    $amount = $spin->getPrizeAmount();

                    $user->updateBonusBalance($amount);

                    $operation = new Operation();
                    $operation->setUser($user);
                    $operation->setAmount($amount);
                    $operation->setType('bonus');
                    $operation->setStatus('complete');

                    $em->persist($user);
                    $em->persist($operation);

                    break;
                case 'cash':
                    // Update user cash balance
                    $amount = $spin->getPrizeAmount();

                    $user->updateBalance($amount);

                    $operation = new Operation();
                    $operation->setUser($user);
                    $operation->setAmount($amount);
                    $operation->setType('win');
                    $operation->setStatus('complete');

                    $em->persist($user);
                    $em->persist($operation);

                    break;
                case 'prize':
                    $prize = $spin->getPrize();

                    $userPrize = new UserPrize();
                    $userPrize->setUser($user);
                    $userPrize->setSpin($spin);
                    $userPrize->setPrize($prize);
                    $userPrize->setStatus('pending');

                    $prize->updateQuantity(-1);

                    $em->persist($userPrize);
                    $em->persist($prize);

                    break;
                default:
                    break;
            }

            $em->flush();

            return new JsonResponse(['success' => true, 'error' => 0, 'message' => 'Successfull update!']);
        }

        return new JsonResponse(['success' => false, 'error' => 404, 'message' => 'Spin not found!']);
    }

    /**
     * @Route("/winwheel/reject/", name="ajax_winwheel_reject_winnings", methods={"GET", "POST"})
     */
    public function winWheelRejectWinningsAction(Request $request)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $id = $request->get('spinId');

        $em = $this->getDoctrine()->getManager();

        $spin = $em->getRepository('AppBundle:WinWheelSpin')->findOneBy([
            'id' => $id,
            'user' => $user,
            'status' => 'pending'
        ]);

        if ($spin) {
            $spin->setStatus('rejected');
            $em->persist($spin);

            switch ($spin->getPrizeType()) {
                case 'prize':
                    // Reject prize
                    $prize = $spin->getPrize();

                    $userPrize = new UserPrize();
                    $userPrize->setUser($user);
                    $userPrize->setPrize($prize);
                    $userPrize->setStatus('rejected');

                    $em->persist($userPrize);
                    break;
                case 'bonus':
                case 'cash':
                default:
                    // Reject spin result
                    break;
            }

            $em->flush();

            return new JsonResponse(['success' => true, 'error' => 0, 'message' => 'Successfull update!']);
        }

        return new JsonResponse(['success' => false, 'error' => 404, 'message' => 'Spin not found!']);
    }

    /**
     * @Route("/balance/convert/", name="ajax_balance_convert", methods={"GET", "POST"})
     */
    public function balanceConvertAction(Request $request)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $amount = $request->get('amount');

        if ($amount) {
            $balance = $user->getBalance();

            if ($amount > $balance) {
                return new JsonResponse(['success' => false, 'error' => 400, 'message' => 'Not enough money']);
            }

            $em = $this->getDoctrine()->getManager();

            $user->updateBalance(-1 * $amount);
            $user->updateBonusBalance($amount / $this->container->getParameter('loyalty_to_cash_rate'));

            $operation = new Operation();
            $operation->setUser($user);
            $operation->setType('exchange');
            $operation->setAmount($amount);
            $operation->setStatus('complete');

            $em->persist($user);
            $em->persist($operation);

            $em->flush();

            return new JsonResponse([
                'success' => true,
                'error' => 0,
                'message' => 'Balance updated',
                'data' => [
                    'balance' => $user->getBalance(),
                    'bonus_balance' => $user->getBonuses()
                ]
            ]);
        }

        return new JsonResponse(['success' => false, 'error' => 500, 'message' => 'Internal error occured']);
    }

    /**
     * @Route("/balance/withdrawal/", name="ajax_balance_withdrawal", methods={"GET", "POST"})
     */
    public function balanceWithdrawalAction(Request $request)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $amount = $request->get('amount');

        if ($amount) {
            $balance = $user->getBalance();

            if ($amount > $balance) {
                return new JsonResponse(['success' => false, 'error' => 400, 'message' => 'Not enough money']);
            }

            $em = $this->getDoctrine()->getManager();

            $user->updateBalance(-1 * $amount);

            $operation = new Operation();
            $operation->setUser($user);
            $operation->setType('withdrawal');
            $operation->setAmount($amount);
            $operation->setStatus('pending');

            $em->persist($user);
            $em->persist($operation);

            $em->flush();

            return new JsonResponse([
                'success' => true,
                'error' => 0,
                'message' => 'Balance updated',
                'data' => [
                    'balance' => $user->getBalance(),
                    'bonus_balance' => $user->getBonuses()
                ]
            ]);
        }

        return new JsonResponse(['success' => false, 'error' => 500, 'message' => 'Internal error occured']);
    }
}
