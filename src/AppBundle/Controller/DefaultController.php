<?php

namespace AppBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity as Entity;
use AppBundle\Entity\User;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
	    $repository = $this->getDoctrine()->getRepository(User::class);
	    $membersCount = $repository->createQueryBuilder('u')
		    ->select('count(u.id)')
		    ->getQuery()
		    ->getSingleScalarResult();

	    $user = $this->get('security.token_storage')->getToken()->getUser();

        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
	        'spins' => 1281,
	        'new_spins' => 2,
	        'members_count' => $membersCount,
	        'winners' => 38,
	        'balance' => $user->getBalance(),
	        'bonuses' => $user->getBonuses(),
	        'payouts' => 10237.28,
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

		// TODO: Add recursion and permissions check
		foreach($items as $item)
		{
			if($item->getParent() == null)
			{
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
