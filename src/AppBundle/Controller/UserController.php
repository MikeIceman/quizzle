<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Form\UserType;

/**
 * User CRUD controller.
 * @package AppBundle\Controller
 * @Route("/admin/user")
 */
class UserController extends Controller
{
    /**
     * Lists all user entities.
     *
     * @Route("/", name="user_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository('AppBundle:User')->findAll();

        return $this->render(
            'user/index.html.twig',
            [
                'users' => $users,
            ]
        );
    }

    /**
     * Creates a new user entity.
     *
     * @Route("/new", name="user_new")
     * @Method({"GET", "POST"})
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function newAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('user_show', ['id' => $user->getId()]);
        }

        return $this->render(
            'user/new.html.twig',
            [
                'user' => $user,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Finds and displays a user entity.
     *
     * @Route("/{id}", name="user_show")
     * @Method("GET")
     * @param User $user
     *
     * @return Response
     */
    public function showAction(User $user)
    {
        $deleteForm = $this->createDeleteForm($user);
        $role = $user->getRoleDescription($user->getHighestRole());

        return $this->render(
            'user/show.html.twig',
            [
                'user' => $user,
                'delete_form' => $deleteForm->createView(),
                'role' => $role
            ]
        );
    }

    /**
     * Displays a form to edit an existing user entity.
     *
     * @Route("/{id}/edit", name="user_edit")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param User $user
     *
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, User $user)
    {
        $deleteForm = $this->createDeleteForm($user);
        $editForm = $this->createForm(UserType::class, $user);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            // TODO: Save user password and role. This is just an example of the code;

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_edit', ['id' => $user->getId()]);
        }

        return $this->render(
            'user/edit.html.twig',
            [
                'user' => $user,
                'edit_form' => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
            ]
        );
    }

    /**
     * Deletes a user entity.
     *
     * @Route("/{id}", name="user_delete")
     * @Method("DELETE")
     * @param Request $request
     * @param User $user
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, User $user)
    {
        $form = $this->createDeleteForm($user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();
        }

        return $this->redirectToRoute('user_index');
    }

    /**
     * Creates a form to delete a user entity.
     *
     * @param User $user The user entity
     *
     * @return FormInterface
     */
    private function createDeleteForm(User $user)
    {
        return $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'user_delete',
                    ['id' => $user->getId()]
                )
            )
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
