<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use AppBundle\Entity\Stock;
use AppBundle\Form\StockType;

/**
 * Stock controller.
 *
 * @Route("/admin/stock")
 */
class StockController extends Controller
{
    /**
     * Lists all Stock entities.
     *
     * @Route("/", name="stock_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $bars = $em->getRepository('AppBundle:Bar')->findAll();

        return $this->render('AppBundle:Stock:index.html.twig', array(
            'bars' => $bars,
        ));
    }

    /**
     * Creates a new Stock entity.
     *
     * @Route("/new", name="stock_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $stock = new Stock();
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm('AppBundle\Form\StockType', $stock);
        $form->add('bar', EntityType::class, array(
                                                    'class' => 'AppBundle:Bar',
                                                    'choice_label' => 'title',
                                                    'label' => "Бар",
                                                    'attr' => array(
                                                            'class' => 'form-control')
                    ));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $is_ingredient = $em->getRepository('AppBundle:Stock')->getIngredientInBar($form->getData()->getIngredient(), $form->getData()->getBar());
            
            if ($is_ingredient) {
                $is_ingredient[0]->setCount($is_ingredient[0]->getCount() + $form->getData()->getCount());
                $em->persist($is_ingredient[0]);
            } else {
                $em->persist($stock);
            }
            
            $em->flush();

            return $this->redirectToRoute('stock_index');
        }

        return $this->render('AppBundle:Stock:new.html.twig', array(
            'stock' => $stock,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Stock entity.
     *
     * @Route("/{id}", name="stock_show")
     * @Method("GET")
     */
    public function showAction(Stock $stock)
    {
        $deleteForm = $this->createDeleteForm($stock);

        return $this->render('AppBundle:Stock:show.html.twig', array(
            'stock' => $stock,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Stock entity.
     *
     * @Route("/{id}/edit", name="stock_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Stock $stock)
    {

        $deleteForm = $this->createDeleteForm($stock);
        $editForm = $this->createForm('AppBundle\Form\StockType', $stock);

        $editForm->add('bar', EntityType::class, array(
                                                    'class' => 'AppBundle:Bar',
                                                    'choice_label' => 'title',
                                                    'label' => "Бар",
                                                    'disabled' => true,
                                                    'attr' => array(
                                                            'class' => 'form-control')
                    ));
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($stock);
            $em->flush();

            return $this->redirectToRoute('stock_index');
        }

        return $this->render('AppBundle:Stock:edit.html.twig', array(
            'stock' => $stock,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Stock entity.
     *
     * @Route("/{id}", name="stock_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Stock $stock)
    {
        $form = $this->createDeleteForm($stock);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($stock);
            $em->flush();
        }

        return $this->redirectToRoute('stock_index');
    }

    /**
     * Creates a form to delete a Stock entity.
     *
     * @param Stock $stock The Stock entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Stock $stock)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('stock_delete', array('id' => $stock->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
