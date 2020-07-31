<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\WinWheelSpin;
use AppBundle\Entity\Prize;

/**
 * Class GamesController
 * @package AppBundle\Controller
 * @Route("/games")
 */
class GamesController extends Controller
{
    /**
     * @Route("/", name="games")
     * @return Response
     */
    public function indexAction()
    {
        return $this->render('games/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
        ]);
    }

    /**
     * @Route("/winwheel/", name="games_winwheel")
     * @return Response
     */
    public function winwheelAction()
    {
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

        //$this->get('session')->set('last_spin_date', $lastSpinDate);

        $prizes = $this->getDoctrine()->getRepository(Prize::class)->findBy([
            'isActive' => true
        ]);

        return $this->render('games/winwheel.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
            'last_spin' => $lastSpinDate,
            'next_spin' => $nextSpinDate,
            'prizes' => $prizes,
        ]);
    }

    /**
     * @Route("/roulette/", name="games_roulette")
     * @return Response
     */
    public function rouletteAction()
    {
        return $this->render('games/roulette.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
        ]);
    }

    /**
     * @Route("/slots/", name="games_slots")
     * @return Response
     */
    public function slotsAction()
    {
        return $this->render('games/slots.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
        ]);
    }

    /**
     * @Route("/lotto/", name="games_lotto")
     * @return Response
     */
    public function lottoAction()
    {
        return $this->render('games/lotto.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
        ]);
    }
}
