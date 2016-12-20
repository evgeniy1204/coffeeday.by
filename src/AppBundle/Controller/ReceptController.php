<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Recept;
use AppBundle\Form\ReceptType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Recept controller.
 *
 * @Route("/admin/recept")
 */
class ReceptController extends Controller
{
    /**
     * Lists all Recept entities.
     *
     * @Route("/", name="recept_index")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $products_ids = array();
        $recepts = array();
        $recept_products = $em->getRepository('AppBundle:Product')->findAllRecept();

        foreach ($recept_products as $key => $value) {
            $recept = $em->getRepository('AppBundle:Recept')->getProducts($value->getId());
           
            foreach ($recept as $keys => $value) {
                $recepts[$value['name']]['recept'][] = array(
                                    "id"         => $value[0]->getId(),
                                    "count"      => $value[0]->getCount(),
                                    "ingredient" => $value[0]->getIngredient()->getName(),
                                    "type"       => $value[0]->getIngredient()->getType());
                $recepts[$value['name']]['cost'] = $value['cost'];
                $recepts[$value['name']]['id'] = $value['id'];
            }
        }
        foreach ($recept_products as $key => $value) {
            $products_ids[] = $value->getId();
        }
        if (!$products_ids) {
            $no_recept_products = $em->getRepository('AppBundle:Product')->findAll();
            return array(
                    'no_recept_products' => $no_recept_products);
        }
        $no_recept_products = $em->getRepository('AppBundle:Product')->findAllNoRecept($products_ids);
        return array(
                    'recept_products' => $recept_products,
                    'no_recept_products' => $no_recept_products,
                    'recepts' => $recepts);
    }

    /**
     * Creates a new Recept entity.
     *
     * @Route("/new", name="recept_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $recept = new Recept();
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm('AppBundle\Form\ReceptType', $recept);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($recept);
            $em->flush();

            $recepts = array();
            foreach ($form->getData()->getProducts() as $key => $value) {
                $id = $value->getId();
            }

            $recept = $em->getRepository('AppBundle:Recept')->getProducts($id);
            foreach ($recept as $key => $value) {
                $recepts[$value['name']]['recept'][] = array(
                                "count"      => $value[0]->getCount(),
                                "ingredient" => $value[0]->getIngredient()->getName(),
                                "type"       => $value[0]->getIngredient()->getType());
                $recepts[$value['name']]['cost'] = $value['cost'];
            }
            
            $recept = new Recept();
            $form = $this->createForm('AppBundle\Form\ReceptType', $recept);

            return $this->render('AppBundle:Recept:new_form.html.twig', array(
                'recepts' => $recepts
            ));
        }

        return $this->render('AppBundle:Recept:new.html.twig', array(
            'recept' => $recept,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Recept entity.
     *
     * @Route("/{id}", name="recept_show")
     * @Method("GET")
     */
    public function showAction(Recept $recept, $id)
    {
        $recepts = array();
        $recept = $em->getRepository('AppBundle:Recept')->getProducts($id);
        foreach ($recept as $key => $value) {
            $recepts[$value['name']]['recept'][] = array(
                            "count"      => $value[0]->getCount(),
                            "ingredient" => $value[0]->getIngredient()->getName(),
                            "type"       => $value[0]->getIngredient()->getType());
            $recepts[$value['name']]['cost'] = $value['cost'];
        }

        $deleteForm = $this->createDeleteForm($recept);

        return $this->render('AppBundle:Recept:show.html.twig', array(
            'recept' => $recept,
            'delete_form' => $deleteForm->createView(),
            'recepts' => $recepts
        ));
    }

    /**
     * Displays a form to edit an existing Recept entity.
     *
     * @Route("/{id}/edit", name="recept_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Recept $recept)
    {
        $deleteForm = $this->createDeleteForm($recept);
        $editForm = $this->createForm('AppBundle\Form\ReceptType', $recept);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($recept);
            $em->flush();

            return $this->redirectToRoute('recept_edit', array('id' => $recept->getId()));
        }

        return $this->render('AppBundle:Recept:edit.html.twig', array(
            'recept' => $recept,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }



    /**
     * Deletes a Recept entity.
     *
     * @Route("/{id}", name="recept_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Recept $recept)
    {
        $form = $this->createDeleteForm($recept);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($recept);
            $em->flush();
        }

        return $this->redirectToRoute('recept_index');
    }

    /**
     * Creates a form to delete a Recept entity.
     *
     * @param Recept $recept The Recept entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Recept $recept)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('recept_delete', array('id' => $recept->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
