<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Bar;
use AppBundle\Form\BarType;

/**
 * Bar controller.
 *
 * @Route("/admin/bar")
 */
class BarController extends Controller
{
    /**
     * Lists all Bar entities.
     *
     * @Route("/", name="bar_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em      = $this->getDoctrine()->getManager();
        $bars    = $em->getRepository('AppBundle:Bar')->findAll();

        if(!$bars) {
            return $this->render('AppBundle:Bar:index.html.twig');
        }

        $date    = new \DateTime();
        $date->modify('-1 day');
        $date->format('Y-m-d');

        foreach ($bars as $key => $value) {
            $total      = $em->getRepository('AppBundle:Check')->getAllEarning($value->getId(), $date);
            $total      = ($total) ? $total[0]['total'] : 0;
            $earnings[] = array(
                                'bar'   => $value,
                                'total' => $total
                                );
        }

        return $this->render('AppBundle:Bar:index.html.twig', array(
                                                                'bars'     => $bars,
                                                                'earnings' => $earnings
                                                            ));
    }

    /**
     * Creates a new Bar entity.
     *
     * @Route("/new", name="bar_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $bar  = new Bar();
        $em   = $this->getDoctrine()->getManager();
        $form = $this->createForm('AppBundle\Form\BarType', $bar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($bar);
            $em->flush();

            return $this->redirectToRoute('bar_show', array(
                'id' => $bar->getId()
                ));
        }

        return $this->render('AppBundle:Bar:new.html.twig', array(
            'bar'  => $bar,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Bar entity.
     *
     * @Route("/{id}", name="bar_show")
     * @Method("GET")
     */
    public function showAction(Bar $bar, $id)
    {
        $date         = new \DateTime();
        $date->modify('-1 day');
        $date->format('Y-m-d');
        $em           = $this->getDoctrine()->getManager();
        $deleteForm   = $this->createDeleteForm($bar);
        $earnings     = $em->getRepository('AppBundle:Check')->getEarningsUser($id);
        $products     = $em->getRepository('AppBundle:Product')->findAll();
        $salesProduct = $em->getRepository('AppBundle:Bin')->getSalesProduct($id);
        $total        = $em->getRepository('AppBundle:Check')->getAllEarning($id, $date);

        return $this->render('AppBundle:Bar:show.html.twig', array(
            'bar'             => $bar,
            'delete_form'     => $deleteForm->createView(),
            'earnings'        => $earnings,
            'products'        => $products,
            'salesProduct'    => $salesProduct,
            'total'           => $total[0]['total']
        ));
    }

    /**
     * Displays a form to edit an existing Bar entity.
     *
     * @Route("/{id}/edit", name="bar_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Bar $bar)
    {
        $deleteForm = $this->createDeleteForm($bar);
        $em         = $this->getDoctrine()->getManager();
        $editForm   = $this->createForm('AppBundle\Form\BarType', $bar);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->persist($bar);
            $em->flush();

            return $this->redirectToRoute('bar_index');
        }

        return $this->render('AppBundle:Bar:edit.html.twig', array(
            'bar'         => $bar,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Bar entity.
     *
     * @Route("/{id}", name="bar_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Bar $bar)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createDeleteForm($bar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->remove($bar);
            $em->flush();
        }

        return $this->redirectToRoute('bar_index');
    }

    /**
     * Creates a form to delete a Bar entity.
     *
     * @param Bar $bar The Bar entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Bar $bar)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('bar_delete', array('id' => $bar->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * @param Bar $id The Bar id
     *
     * @Route("/{id}/stat", name="stat_product")
     * @Method("POST")
     */
    public function statProductAction(Request $request, $id)
    {
        $em         = $this->getDoctrine()->getManager();
        $product    = $request->request->get('product');
        $start_date = $request->request->get('start_date');
        $end_date   = $request->request->get('end_date');
        $products   = $em->getRepository('AppBundle:Bin')->getAllBinProducts($id, $product, $start_date, $end_date);

        return $this->render('AppBundle:Bar:stat.html.twig', array(
                                                                    'products' => $products,
                                                                ));
    }

    /**
     *
     * @Route("/stat/sales", name="bar_sales")
     * @Method("POST")
     */
    public function statSalesAction(Request $request)
    {
        $em         = $this->getDoctrine()->getManager();
        $bar        = $request->request->get('bar');
        $start_date = $request->request->get('start_date');
        $end_date   = $request->request->get('end_date');

        $sales = $em->getRepository('AppBundle:Check')->getSalesBar($bar, $start_date, $end_date);
        
        return new Response($sales[0]['total']);
    }

    /**
     *
     * @Route("/stat/sales/product", name="bar_sales_produt")
     * @Method("POST")
     */
    public function statSalesProductAction(Request $request)
    {
        $em         = $this->getDoctrine()->getManager();
        $bar        = $request->request->get('bar');
        $start_date = $request->request->get('start_date');
        $end_date   = $request->request->get('end_date');

        $salesProduct = $em->getRepository('AppBundle:Bin')->getSalesProduct($bar, $start_date, $end_date);
        
        return $this->render('AppBundle:Bar:sales_product.html.twig', array(
                                                                    'salesProduct' => $salesProduct,
                                                                ));
    }



}
