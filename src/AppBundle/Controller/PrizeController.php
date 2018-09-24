<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Prize;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Prize controller.
 *
 * @Route("/admin/prize")
 */
class PrizeController extends Controller
{
    /**
     * Lists all prize entities.
     *
     * @Route("/", name="prize_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $prizes = $em->getRepository('AppBundle:Prize')->findAll();

        return $this->render('prize/index.html.twig', array(
            'prizes' => $prizes,
        ));
    }

    /**
     * Creates a new prize entity.
     *
     * @Route("/new", name="prize_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $prize = new Prize();
        $form = $this->createForm('AppBundle\Form\PrizeType', $prize);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($prize);
            $em->flush();

            return $this->redirectToRoute('prize_show', array('id' => $prize->getId()));
        }

        return $this->render('prize/new.html.twig', array(
            'prize' => $prize,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a prize entity.
     *
     * @Route("/{id}", name="prize_show")
     * @Method("GET")
     */
    public function showAction(Prize $prize)
    {
        $deleteForm = $this->createDeleteForm($prize);

        return $this->render('prize/show.html.twig', array(
            'prize' => $prize,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing prize entity.
     *
     * @Route("/{id}/edit", name="prize_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Prize $prize)
    {
        $deleteForm = $this->createDeleteForm($prize);
        $editForm = $this->createForm('AppBundle\Form\PrizeType', $prize);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('prize_edit', array('id' => $prize->getId()));
        }

        return $this->render('prize/edit.html.twig', array(
            'prize' => $prize,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a prize entity.
     *
     * @Route("/{id}", name="prize_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Prize $prize)
    {
        $form = $this->createDeleteForm($prize);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($prize);
            $em->flush();
        }

        return $this->redirectToRoute('prize_index');
    }

    /**
     * Creates a form to delete a prize entity.
     *
     * @param Prize $prize The prize entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Prize $prize)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('prize_delete', array('id' => $prize->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
