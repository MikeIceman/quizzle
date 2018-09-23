<?php

namespace AppBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity as Entity;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="dashboard")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('dashboard/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
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

    public function renderSidebarAction()
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
		/*
		foreach($items as $item)
		{
			if($item->getParent() != null)
			{
				$menu[$item->getParent()]->childs[$item->getId()] = $item;
			}
		}
		*/
        return $this->render('default/sidebar.html.twig', [
        	'menu' => $menu
        ]);
    }

    public function renderBreadcrumbsAction()
    {
        return $this->render('default/breadcrumbs.html.twig');
    }

    public function renderAsideAction()
    {
        return $this->render('default/aside.html.twig');
    }
}
