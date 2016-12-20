<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use AppBundle\Entity\Check;

/**
 * @Route("/")
 */
class AdminController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $bins = $em->getRepository('AppBundle:Bin')->findAll();
        $bars = $em->getRepository('AppBundle:Bar')->findAll();
        $users = $em->getRepository('AppBundle:User')->findAll();
        $user = $this->getUser();
        $date = new \DateTime();
        $date->format('Y-m-d');
        $session = $request->getSession();
        
        if ($this->getUser()->hasRole('ROLE_BARISTA') && $session->get('user') == 0) {
            return $this->render('AppBundle:Admin:lock.html.twig', array(
                                                                'bars'    => $bars
                                                                ));
        }
        //$earning = ($this->getUser()->hasRole('ROLE_BARISTA')) ? $em->getRepository('AppBundle:Check')->getEarningBar($this->getUser()->getBar(), $date) : $em->getRepository('AppBundle:Check')->getAllEarningBars();
        
        $earning = $em->getRepository('AppBundle:Check')->getAllEarningBars();
        $earning = ($earning) ? $earning[0]['total'] : 0;
        

        return $this->render('AppBundle:Admin:index.html.twig', array(
                                                                'bins'    => $bins,
                                                                'users'   => $users,
                                                                'earning' => $earning,
                                                                'bars'    => $bars
                                                                ));
    }

    /**
     * @Route("/set-bar", name="set_bar")
     */
    public function setBarAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $bars = $em->getRepository('AppBundle:Bar')->findAll();
        $user = $this->getUser();
        if (!$user) {
            return new Response('Пользователь не найден', 500, array(
                                                    'X-Status-Code' => 500
                                                ));
        }
        $bar = $em->getRepository('AppBundle:Bar')->find($request->request->get('id'));
        $user->setBar($bar);
        $em->flush();

        $session = $request->getSession();
        $session->start();
        $session->set('user', 1);
        return new Response('success', 200, array( 'X-Status-Code' => 200));
    }

    /**
     * @Route("/check-delete", name="check_delete")
     */
    public function checkDeleteAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $check = $em->getRepository('AppBundle:Check')->find($request->request->get('check'));
        $user =  $em->getRepository('AppBundle:User')->find($request->request->get('user'));
        $em->remove($check);
        $em->flush();

        $date = new \DateTime();
        $start_date = "";
        $end_date = "";

        $checks = $em->getRepository('AppBundle:Check')->findAllChecks($start_date, $end_date, $user);
        
        return $this->render('AppBundle:Admin:checksFilter.html.twig', array(
                                                                'checks'    => $checks,
                                                                'user'      => $user
                                                                ));
    }
}
