<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\Product;
use AppBundle\Form\ProductType;

/**
 * Product controller.
 *
 * @Route("/admin/product")
 */
class ProductController extends Controller
{
    /**
     * Lists all Product entities.
     *
     * @Route("/", name="product_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $prod = array();
        $em = $this->getDoctrine()->getManager();
        $products = $em->getRepository('AppBundle:Product')->findAll();
        /*$coffe = $em->getRepository('AppBundle:Recept')->getProducts(2);
       
        foreach ($coffe as $key => $value) {
            $prod[$value['name']][] = array(
                            "count" => $value[0]->getCount(),
                            "ingredient" => $value[0]->getIngredient()->getName(),
                            "type" => $value[0]->getIngredient()->getType());
            
        }*/
        return $this->render('AppBundle:Product:index.html.twig', array(
            'products' => $products
        ));
    }

    /**
     * Creates a new Product entity.
     *
     * @Route("/new", name="product_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $product = new Product();
        $form = $this->createForm('AppBundle\Form\ProductType', $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('product_show', array('id' => $product->getId()));
        }

        return $this->render('AppBundle:Product:new.html.twig', array(
            'product' => $product,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Product entity.
     *
     * @Route("/{id}", name="product_show")
     * @Method("GET")
     */
    public function showAction(Product $product, $id)
    {
        $recepts = array();
        $em = $this->getDoctrine()->getManager();
        $recept = $em->getRepository('AppBundle:Recept')->getProducts($id);
        $sales = $em->getRepository('AppBundle:Bin')->findByProduct($id);
        foreach ($recept as $key => $value) {
            $recepts[$value['name']]['recept'][] = array(
                            "count"      => $value[0]->getCount(),
                            "ingredient" => $value[0]->getIngredient()->getName(),
                            "type"       => $value[0]->getIngredient()->getType());
            $recepts[$value['name']]['cost'] = $value['cost'];
        }

        $deleteForm = $this->createDeleteForm($product);

        return $this->render('AppBundle:Product:show.html.twig', array(
            'product'     => $product,
            'delete_form' => $deleteForm->createView(),
            'recepts'     => $recepts,
            'sales'       => $sales
        ));
    }

    /**
     * Displays a form to edit an existing Product entity.
     *
     * @Route("/{id}/edit", name="product_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Product $product, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $recept_products = $em->getRepository('AppBundle:Product')->findReceptProduct($id);
        $recepts = array();
        if ($recept_products) {
        	foreach ($recept_products as $key => $value) {
	            $recept = $em->getRepository('AppBundle:Recept')->getProducts($id);
	            
	            foreach ($recept as $key => $value) {
	                $recepts[] = array(
	                                    "id"         => $value[0]->getId(),
	                                    "count"      => $value[0]->getCount(),
	                                    "ingredient" => $value[0]->getIngredient()->getName(),
	                                    "type"       => $value[0]->getIngredient()->getType());
	            }
	        }
        }
        

        $deleteForm = $this->createDeleteForm($product);
        $editForm = $this->createForm('AppBundle\Form\ProductType', $product);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('product_index');
        }

        return $this->render('AppBundle:Product:edit.html.twig', array(
            'product' => $product,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'recepts'   => $recepts
        ));
    }

    /**
     * @Route("/edit/custom", name="recept_custom_edit")
     * @Method({"GET", "POST"})
     */
    public function editCostomAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $recept = $em->getRepository('AppBundle:Recept')->find($request->request->get('id'));
        $recept->setCount($request->request->get('count'));
        $em->flush();

        $recept_products = $em->getRepository('AppBundle:Product')->findReceptProduct($request->request->get('product'));
        foreach ($recept_products as $key => $value) {
            $recept = $em->getRepository('AppBundle:Recept')->getProducts($request->request->get('product'));
            
            foreach ($recept as $key => $value) {
                $recepts[] = array(
                                    "id"         => $value[0]->getId(),
                                    "count"      => $value[0]->getCount(),
                                    "ingredient" => $value[0]->getIngredient()->getName(),
                                    "type"       => $value[0]->getIngredient()->getType());
            }
        }
        
        $product = $em->getRepository('AppBundle:Product')->find($request->request->get('product'));
        return $this->render('AppBundle:Product:recept.html.twig', array(
            'recepts' => $recepts,
            'product' => $product
        ));
    }

    /**
     * Deletes a Product entity.
     *
     * @Route("/{id}", name="product_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Product $product, $id)
    {
        $em = $this->getDoctrine()->getManager();
        //$product = $em->getRepository('AppBundle:Product')->find($id);

       /* if (!$product) {
            throw $this->createNotFoundException('No task found for id '.$id);
        }*/

   /*     $originalTags = new ArrayCollection();
        $originalTags->add($product->getCheck());
        

        
        dump($originalTags);
die;*/
        $form = $this->createDeleteForm($product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $em->remove($product);
            $em->flush();
        }

        return $this->redirectToRoute('product_index');
    }

    /**
     * Creates a form to delete a Product entity.
     *
     * @param Product $product The Product entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Product $product)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('product_delete', array('id' => $product->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
