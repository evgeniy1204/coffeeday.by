<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Reception;
use AppBundle\Form\ReceptionType;

/**
 * Reception controller.
 *
 * @Route("/barista/reception")
 */
class ReceptionController extends Controller
{
    /**
     * Lists all Reception entities.
     *
     * @Route("/", name="reception_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        //$user = $em->getRepository('AppBundle:User')->find(1);
        $date = new \DateTime();
        $start_date = $date->format('Y-m-d');
        $end_date = "";
        $receptions =  ($user->hasRole('ROLE_ADMIN')) ? $em->getRepository('AppBundle:Reception')->getAllReception($start_date) : $em->getRepository('AppBundle:Reception')->findByUser($this->getUser());

        return $this->render('AppBundle:Reception:index.html.twig', array(
            'receptions' => $receptions,
        ));
    }

    /**
     * Lists all WriteOff entities.
     *
     * @Route("/exel", name="reception_index_exel")
     * @Method({"GET","POST"})
     */
    public function indexExelAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $date = new \DateTime();
        $start_date = $date->format('Y-m-d');
        $end_date = "";

        $bars = $em->getRepository('AppBundle:Bar')->findAll();
        return $this->render('AppBundle:Reception:filterIndex.html.twig', array(
            'bars'      => $bars
        ));
    }

    /**
     * Creates a new Reception entity.
     *
     * @Route("/new", name="reception_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $reception = new Reception();
        $user = $this->getUser();
        $form = $this->createForm('AppBundle\Form\ReceptionType', $reception);
        $form->add('stock', EntityType::class, array(
                                            'label' => "Выберите продукт",
                                            'class' => 'AppBundle:Stock',
                                            'query_builder' => function(EntityRepository $er) use ($user) {
                                                return $er->createQueryBuilder('s')
                                                          ->where('s.bar = :bar')
                                                          ->setParameter('bar', $user->getBar());
                                             },
                                            'choice_label' => 'ingredient.name',
                                            'attr' => array(
                                                'class' => 'form-control')
                                        ));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $stock = $em->getRepository('AppBundle:Stock')->find($form->getData()->getStock());
            $stock->setCount($stock->getCount() + $form->getData()->getCount());
            $reception->setUser($user);
            $em->persist($stock);
            $em->persist($reception);
            $em->flush();

            return $this->redirectToRoute('reception_index');
        }

        return $this->render('AppBundle:Reception:new.html.twig', array(
            'reception' => $reception,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Reception entity.
     *
     * @Route("/{id}", name="reception_show")
     * @Method("GET")
     */
    public function showAction(Reception $reception)
    {
        $deleteForm = $this->createDeleteForm($reception);

        return $this->render('AppBundle:Reception:show.html.twig', array(
            'reception' => $reception,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Reception entity.
     *
     * @Route("/{id}/edit", name="reception_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Reception $reception)
    {
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($reception);
        $editForm = $this->createForm('AppBundle\Form\ReceptionType', $reception);
        $user = $this->getUser();
        $editForm->add('stock', EntityType::class, array(
                                            'label' => "Выберите продукт",
                                            'class' => 'AppBundle:Stock',
                                            'query_builder' => function(EntityRepository $er) use ($user) {
                                                return $er->createQueryBuilder('s')
                                                          ->where('s.bar = :bar')
                                                          ->setParameter('bar', $user->getBar());
                                             },
                                            'choice_label' => 'ingredient.name',
                                            'attr' => array(
                                                'class' => 'form-control')
                                        ));
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            
            $em->persist($reception);
            $em->flush();

            return $this->redirectToRoute('reception_index');
        }

        return $this->render('AppBundle:Reception:edit.html.twig', array(
            'reception' => $reception,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Reception entity.
     *
     * @Route("/{id}", name="reception_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Reception $reception)
    {
        $form = $this->createDeleteForm($reception);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($reception);
            $em->flush();
        }

        return $this->redirectToRoute('reception_index');
    }

    /**
     * Creates a form to delete a Reception entity.
     *
     * @param Reception $reception The Reception entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Reception $reception)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('reception_delete', array('id' => $reception->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * @Route("/reception-filter", name="reception_filter")
     */
    public function receptionFilterAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $start_date = $request->request->get('start_date');
        $end_date = $request->request->get('end_date');
        $bar = $request->request->get('bar');
        /*dump($request->request->get('bar'));
        die;*/
        $reception =  $em->getRepository('AppBundle:Reception')->getReception($start_date, $end_date, $bar, $isGroup = 0);
        $receptionsIsGroup =  $em->getRepository('AppBundle:Reception')->getReception($start_date, $end_date, $bar, $isGroup = 1);
        if ($receptionsIsGroup) {
            $row = 1;
            $col = 2;
            $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

            $phpExcelObject->getProperties()->setCreator("liuggio")
                   ->setLastModifiedBy("Giulio De Donato")
                   ->setTitle("Office 2005 XLSX Test Document")
                   ->setSubject("Office 2005 XLSX Test Document")
                   ->setDescription("Test document for Office 2005 XLSX, generated using PHP classes.")
                   ->setKeywords("office 2005 openxml php")
                   ->setCategory("Test result file");
            
            $phpExcelObject->setActiveSheetIndex(0)
                   ->setCellValue('A1', 'id')
                   ->setCellValue('B1', 'Наименование')
                   ->setCellValue('C1', 'Количество')
                   ->setCellValue('D1', 'Бар');
            foreach ($receptionsIsGroup as $key => $value) {
                $phpExcelObject->setActiveSheetIndex(0)
                   ->setCellValue('A'.$col, $value[0]->getId())
                   ->setCellValue('B'.$col, $value[0]->getStock()->getIngredient()->getName())
                   ->setCellValue('C'.$col, $value['count_prod'])
                   ->setCellValue('D'.$col, $value[0]->getStock()->getBar()->getTitle());
                   $col++;
            }
            $phpExcelObject->getActiveSheet()->setTitle('Simple');
               // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $phpExcelObject->setActiveSheetIndex(0);

                // create the writer
            $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel2007');
            $writer->save('reception.xlsx');
        }
        return $this->render('AppBundle:Reception:filter.html.twig', array(
                                                                    'receptions' => $reception,
                                                                    'receptionsIsGroup' => $receptionsIsGroup
                                                                    ));
    }

}
