<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\User;
use AppBundle\Form\UserType;

/**
 * User controller.
 *
 * @Route("/admin/user")
 */
class UserController extends Controller
{
    /**
     * Lists all User entities.
     *
     * @Route("/", name="user_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('AppBundle:User')->findAll();

        return $this->render('AppBundle:User:index.html.twig', array(
            'users' => $users,
        ));
    }

    /**
     * Creates a new User entity.
     *
     * @Route("/new", name="user_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm('AppBundle\Form\UserType', $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $user->setRoles(array('ROLE_BARISTA'));
            $user->setEnabled(0);
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('user_show', array('id' => $user->getId()));
        }

        return $this->render('AppBundle:User:new.html.twig', array(
            'user' => $user,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a User entity.
     *
     * @Route("/{id}", name="user_show", requirements={"id": "\d+"})
     * @Method({"GET","POST"})
     */
    public function showAction(User $user, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($user);
        $filter = (null != $request->request->get('filter')) ? $request->request->get('filter') : "";
        $date = new \DateTime();
        $start_date = $date->format('Y-m-d');
        $end_date = "";
        $checks = $em->getRepository('AppBundle:Check')->findAllChecks($start_date, $end_date, $user);

        if ($filter) {
            if ($request->request->get('button') == 'month') {
                $start_date = $date->modify('-1 month');
                $start_date = $start_date->format('Y-m-d');
            } elseif ($request->request->get('button') == 'week') {
                $start_date = $date->modify('-14 day');
                $start_date = $start_date->format('Y-m-d');
            } elseif ($request->request->get('button') == 'today') {
                $start_date = new \DateTime();
                $start_date = $start_date->format('Y-m-d');
            }

            if ($request->request->get('start_date') != "" && $request->request->get('end_date') != "") {
                $start_date = $request->request->get('start_date');
                $end_date   = $request->request->get('end_date');
            }

            $checks = $em->getRepository('AppBundle:Check')->findAllChecks($start_date, $end_date, $user);

            return $this->render('AppBundle:Admin:checksFilter.html.twig', array(
                                                                    'checks' => $checks,
                                                                    'user'   => $user
                                                                    ));
        }
        
        return $this->render('AppBundle:User:show.html.twig', array(
            'user'        => $user,
            'delete_form' => $deleteForm->createView(),
            'checks'      => $checks
        ));
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     * @Route("/{id}/edit", name="user_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, User $user)
    {
        $deleteForm = $this->createDeleteForm($user);
        $editForm = $this->createForm('AppBundle\Form\UserType', $user);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('user_edit', array('id' => $user->getId()));
        }

        return $this->render('AppBundle:User:edit.html.twig', array(
            'user' => $user,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a User entity.
     *
     * @Route("/{id}", name="user_delete")
     * @Method("DELETE")
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
     * Creates a form to delete a User entity.
     *
     * @param User $user The User entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(User $user)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('user_delete', array('id' => $user->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * @Route("/enabled-user", name="enabled_user")
     * @Method("POST")
     */
    public function enabledUserAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')->find($request->request->get('id'));
        
        if ($user->isEnabled()) {
            $user->setEnabled(0);
        } else {
            $user->setEnabled(1);
        }
        $em->flush();
        
        return new Response("success", 201);
    }
}
