<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
     * @return Response
     */
    public function indexAction()
    {
        // Profile
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $role = $user->getRoleDescription($user->getHighestRole());

        $repository = $this->getDoctrine()->getRepository(WinWheelSpin::class);
        $games = $repository->createQueryBuilder('g')
            ->select("COUNT(g.id)")
            ->where('g.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();

        $wins = $repository->createQueryBuilder('g')
            ->select("COUNT(g.id)")
            ->where('g.user = :user')
            ->andWhere('g.prizeType = :type')
            ->setParameter('user', $user)
            ->setParameter('type', 'prize')
            ->getQuery()
            ->getSingleScalarResult();

        $repository = $this->getDoctrine()->getRepository(Operation::class);
        $withdrawals = $repository->createQueryBuilder('o')
            ->select("COALESCE(SUM(o.amount), 0)")
            ->where('o.user = :user')
            ->andWhere('o.type = :type')
            ->andWhere('o.status = :status')
            ->setParameter('user', $user)
            ->setParameter('type', 'withdrawal')
            ->setParameter('status', 'complete')
            ->getQuery()
            ->getSingleScalarResult();

        return $this->render(
            'personal/index.html.twig',
            [
                'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
                'user' => $user,
                'role' => $role,
                'games' => $games,
                'wins' => $wins,
                'withdrawals' => $withdrawals
            ]
        );
    }

    /**
     * @Route("/winnings/", name="personal_winnings")
     * @return Response
     */
    public function winningsAction()
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
            'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
            'winnings' => $winnings
        ]);
    }

    /**
     * @Route("/prizes/", name="personal_prizes")
     * @return Response
     */
    public function prizesAction()
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

        return $this->render(
            'personal/prizes.html.twig',
            [
                'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
                'prizes' => $prizes
            ]
        );
    }

    /**
     * @Route("/operations/", name="personal_operations")
     * @return Response
     */
    public function operationsAction()
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $repository = $this->getDoctrine()->getRepository(Operation::class);

        // Get latest spin
        $operations = $repository->findBy(
            [
                'user' => $user,
                'type' => ['bonus', 'win', 'exchange']
            ],
            ['id' => 'DESC']
        );

        return $this->render('personal/operations.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
            'operations' => $operations,
            'conversion_rate' => $this->container->getParameter('loyalty_to_cash_rate')
        ]);
    }

    /**
     * @Route("/withdrawals/", name="personal_withdrawals")
     * @return Response
     */
    public function withdrawalsAction()
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $repository = $this->getDoctrine()->getRepository(Operation::class);

        // Get latest spin
        $withdrawals = $repository->findBy(
            [
                'user' => $user,
                'type' => 'withdrawal'
            ],
            ['id' => 'DESC']
        );

        // Deprecated: no more need this action because all withdrawals are in "Operations" wiew
        return $this->render(
            'personal/withdrawals.html.twig',
            [
                'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
                'withdrawals' => $withdrawals,
            ]
        );
    }
}
