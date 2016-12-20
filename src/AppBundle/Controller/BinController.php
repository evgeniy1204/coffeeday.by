<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use AppBundle\Entity\Bin;
use AppBundle\Entity\Check;
use AppBundle\Form\BinType;

/**
 * Bar controller.
 *
 * @Route("/barista/bin")
 */
class BinController extends Controller
{
    /**
     * Lists all Bar entities.
     *
     * @Route("/", name="bin_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em   = $this->getDoctrine()->getManager();
        $bars = $em->getRepository('AppBundle:Bar')->findAll();

        return $this->render('AppBundle:Bar:index.html.twig', array(
            'bars' => $bars,
        ));
    }

    /**
     * @Route("/sale", name="sale")
     */
    public function saleAction(Request $request)
    {
        $session     = $request->getSession();
        $session->start();
        $totalAmount = 0;
        $em          = $this->getDoctrine()->getManager();
        $products    = $session->get('products');
        $categories  = $em->getRepository('AppBundle:Category')->findAll();

        $date        = new \DateTime();
        $date        = $date->format('Y-m-d');
        
        $profit      = $em->getRepository('AppBundle:Check')->getProfit($this->getUser(), $date);
        $profit      = ($profit) ? $profit[0]['total'] : 0;
        if ($products) {
            foreach ($products as $key => $value) {
                $totalAmount += $value->getCost();
            }
        }

        return $this->render('AppBundle:Admin:sale.html.twig', array(
                                                                    'categories' => $categories,
                                                                    'products'   => $products,
                                                                    'profit'     => $profit,
                                                                    'total'      => $totalAmount
                                                                    ));
    }

    /**
     * Creates a new Bar entity.
     *
     * @Route("/new", name="bin_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $totalAmount = 0;
        $i = 0;
        $session = $request->getSession();
        $session->start();
        if ($request->request->get('add_to_card')) {
            $products = $session->get('products');

            $product = $em->getRepository('AppBundle:Product')->find($request->request->get('add_to_card'));
            if ($request->request->get('is_free')) {
                $product->setCost(0);
            }
            $products[] = $product;
            $session->set('products', $products);
            
            foreach ($products as $key => $value) {
                $totalAmount = (!$request->request->get('is_free')) ? $totalAmount += $value->getCost() : 0;
                $is_free = $request->request->get('is_free');
            }
            return $this->render('AppBundle:Bin:card.html.twig', array(
                                                                        'products' => $products,
                                                                        'total'    => $totalAmount,
                                                                        'is_free'  =>  $is_free
                                                                    ));
        }

        if ($request->request->get('save')) {
            if ($session->get('products')) {
                $is_free = 0;
                $user = $this->getUser();
                $check = new Check();
                $check->setUser($this->getUser());
                foreach ($session->get('products') as $key => $value) {
                    $totalAmount = $totalAmount += $value->getCost();
                }
                
                $check->setTotal($totalAmount);
                $check->setBar($this->getUser()->getBar());
                $em->persist($check);

                foreach ($session->get('products') as $key => $value) {
                    $product = $em->getRepository('AppBundle:Product')->find($value->getId());
                    if (!$value->getCost()) {
                        $is_free = 1;
                    }
                    foreach ($product->getRecept() as $keys => $values) {
                        $ingredient = $em->getRepository('AppBundle:Stock')->findOneBy(array('ingredient' => $values->getIngredient()->getId(), 'bar' => $user->getBar()));
                        if (!$ingredient || $ingredient->getCount() < $values->getCount() ) {
                            return new Response("Ингредиента " . '"' . $values->getIngredient()->getName() . '" в "' . $product->getName() . '" нет на складе либо не достаточно для этого товара!', 404, array(
                                                    'X-Status-Code' => 404
                                                )); 
                        }
                        
                        $count = ($ingredient->getCount() - $values->getCount() >= 0) ? $ingredient->getCount() - $values->getCount() : 0 ;
                        $ingredient->setCount($count);
                        $em->persist($ingredient);
                        
                    }
                    
                    $bin = new Bin();
                    $bin->setProduct($product);
                    $bin->setIsFree($is_free);
                    $bin->setCheck($check);

                    $em->persist($bin);
                }
                $em->flush();
                $session->clear();
                return new Response("success");
            }
            return new Response("failed");
        }
        

    }

    /**
     * Creates a new Bar entity.
     *
     * @Route("/delete", name="bin_delete")
     * @Method({"GET", "POST"})
     */
    public function deleteAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $totalAmount = 0;
        $i = 0;
        $session = $request->getSession();
        $session->start();

        if ($request->request->get('delete')) {
            $products = $session->get('products');

            if (!$products) {
                return $this->render('AppBundle:Bin:card.html.twig', array(
                                                                        'products' => $products,
                                                                    ));
            }
            foreach ($products as $key => $value) {
                if ($value->getId() == $request->request->get('id')) {
                    unset($products[$key]);
                    break;
                }
            }

            $session->set('products', $products);
            foreach ($products as $key => $value) {
               if ($i != 6) {
                    $totalAmount += $value->getCost();
                }
               $i++;
            }
            return $this->render('AppBundle:Bin:card.html.twig', array(
                                                                        'products' => $products,
                                                                        'total'    => $totalAmount
                                                                    ));
        }

    }
    

    /**
     * @Route("/checks", name="check")
     */
    public function checkAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $filter = (null != $request->request->get('filter')) ? $request->request->get('filter') : "";
        $date = new \DateTime();
        $start_date = "";
        $end_date = "";
        $user = $this->getUser();
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
                                                                    'checks' => $checks
                                                                    ));
        }

        return $this->render('AppBundle:Admin:check.html.twig', array(
                                                                    'checks' => $checks));
    }
}
