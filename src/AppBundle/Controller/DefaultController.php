<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity as Entity;
use AppBundle\Entity\User;
use AppBundle\Entity\WinWheelSpin;
use AppBundle\Entity\UserPrize;
use AppBundle\Entity\Operation;

/**
 * Class DefaultController
 * @package AppBundle\Controller
 * @package AppBundle\Controller
 */
class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @return Response
     */
    public function indexAction()
    {
        $repository = $this->getDoctrine()->getRepository(User::class);
        $membersCount = $repository->createQueryBuilder('u')
            ->select('count(u.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $user = $this->get('security.token_storage')->getToken()->getUser();


        $spins = $this->getDoctrine()
            ->getRepository(WinWheelSpin::class)
            ->createQueryBuilder('s')
            ->select('count(s.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $winners = $this->getDoctrine()
            ->getRepository(UserPrize::class)
            ->createQueryBuilder('p')
            ->select('count(p.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $payouts = $this->getDoctrine()
            ->getRepository(Operation::class)
            ->createQueryBuilder('o')
            ->select('sum(o.amount)')
            ->where('o.type = :type')
            ->andWhere('o.status = :status')
            ->setParameter('type', "withdrawal")
            ->setParameter('status', "complete")
            ->getQuery()
            ->getSingleScalarResult();


        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
            'spins' => $spins,
            'new_spins' => 0,
            'members_count' => $membersCount,
            'winners' => $winners,
            'balance' => $user->getBalance(),
            'bonuses' => $user->getBonuses(),
            'payouts' => $payouts,
            'convert_rate' => $this->container->getParameter('loyalty_to_cash_rate')
        ]);
    }

    public function renderHeaderAction()
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $role = $user->getRoleDescription($user->getHighestRole());
        return $this->render('default/header.html.twig', [
            'user' => $user,
            'role' => $role
        ]);
    }

    public function renderSidebarAction($currentRoute = 'homepage')
    {
        $items = $this->getDoctrine()->getRepository(Entity\Menu::class)->findAll();
        $menu = [];

        // TODO: Need to add recursion and permissions check to use dynamic menu constructor
        foreach ($items as $item) {
            if ($item->getParent() == null) {
                $menu[$item->getId()] = $item;
            }
        }

        return $this->render('default/sidebar.html.twig', [
            'menu' => $menu,
            'curroute' => $currentRoute
        ]);
    }

    public function renderBreadcrumbsAction()
    {
        return $this->render('default/breadcrumbs.html.twig');
    }
}
