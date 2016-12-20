<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\WriteOff;
use AppBundle\Form\WriteOffType;

/**
 * WriteOff controller.
 *
 * @Route("/barista/writeoff")
 */
class WriteOffController extends Controller
{
    /**
     * Lists all WriteOff entities.
     *
     * @Route("/", name="writeoff_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $date = new \DateTime();
        $start_date = $date->format('Y-m-d');
        $end_date = "";
        $writeOffs =  ($user->hasRole('ROLE_ADMIN')) ? $em->getRepository('AppBundle:WriteOff')->getAllWriteOff($start_date) : $em->getRepository('AppBundle:WriteOff')->findByUser($user);
        $bars = $em->getRepository('AppBundle:Bar')->findAll();
        return $this->render('AppBundle:WriteOff:index.html.twig', array(
            'writeOffs' => $writeOffs,
            'bars'      => $bars
        ));
    }

    /**
     * Lists all WriteOff entities.
     *
     * @Route("/exel", name="writeoff_index_exel")
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
        return $this->render('AppBundle:WriteOff:filterIndex.html.twig', array(
            'bars'      => $bars
        ));
    }


    /**
     * Creates a new WriteOff entity.
     *
     * @Route("/new", name="writeoff_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $writeOff = new WriteOff();
        $user = $this->getUser();
        $form = $this->createForm('AppBundle\Form\WriteOffType', $writeOff);
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
                                        ))
            ->add('count', null, array(
                                        'label' => "Количество",
                                        'attr' => array(
                                                'class' => 'form-control')));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $stock = $em->getRepository('AppBundle:Stock')->find($form->getData()->getStock());
            if ($stock) {
                $count = ($stock->getCount() - $form->getData()->getCount() >= 0) ? $stock->getCount() - $form->getData()->getCount() : 0 ;
                $stock->setCount($count);
                $em->persist($stock);
                $em->flush();
            }
            
            $writeOff->setUser($user);
            $em = $this->getDoctrine()->getManager();
            $em->persist($writeOff);
            $em->flush();

            return $this->redirectToRoute('writeoff_index');
        }

        return $this->render('AppBundle:WriteOff:new.html.twig', array(
            'writeOff' => $writeOff,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a WriteOff entity.
     *
     * @Route("/{id}", name="writeoff_show")
     * @Method("GET")
     */
    public function showAction(WriteOff $writeOff)
    {
        $deleteForm = $this->createDeleteForm($writeOff);

        return $this->render('AppBundle:WriteOff:show.html.twig', array(
            'writeOff' => $writeOff,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing WriteOff entity.
     *
     * @Route("/{id}/edit", name="writeoff_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, WriteOff $writeOff)
    {
        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($writeOff);
        $editForm = $this->createForm('AppBundle\Form\WriteOffType', $writeOff);
        $user = $this->getUser();
        $editForm
            ->add('count', null, array(
                                        'label' => "Количество",
                                        'attr' => array(
                                                'class' => 'form-control')));
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $stock = $em->getRepository('AppBundle:Stock')->find($editForm->getData()->getStock());
            if ($stock) {
                $count = ($stock->getCount() - $editForm->getData()->getCount() >= 0) ? $stock->getCount() - $editForm->getData()->getCount() : 0 ;
                $stock->setCount($count);
                $em->persist($stock);
                $em->flush();
            }
            
            $em->persist($writeOff);
            $em->flush();

            return $this->redirectToRoute('writeoff_index');
        }

        return $this->render('AppBundle:WriteOff:edit.html.twig', array(
            'writeOff' => $writeOff,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a WriteOff entity.
     *
     * @Route("/{id}", name="writeoff_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, WriteOff $writeOff)
    {
        $form = $this->createDeleteForm($writeOff);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($writeOff);
            $em->flush();
        }

        return $this->redirectToRoute('writeoff_index');
    }

    /**
     * Creates a form to delete a WriteOff entity.
     *
     * @param WriteOff $writeOff The WriteOff entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(WriteOff $writeOff)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('writeoff_delete', array('id' => $writeOff->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * @Route("/write-off-filter", name="write_off_filer")
     */
    public function writeOffFilterAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $start_date = $request->request->get('start_date');
        $end_date = $request->request->get('end_date');
        $bar = $request->request->get('bar');
        /*dump($request->request->get('bar'));
        die;*/
        $writeOffs =  $em->getRepository('AppBundle:WriteOff')->getWriteOff($start_date, $end_date, $bar, $isGroup = 0);
        $writeOffsIsGroup =  $em->getRepository('AppBundle:WriteOff')->getWriteOff($start_date, $end_date, $bar, $isGroup = 1);
        if ($writeOffsIsGroup) {
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
            foreach ($writeOffsIsGroup as $key => $value) {
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
            $writer->save('writeOff.xlsx');
        }
        return $this->render('AppBundle:WriteOff:filter.html.twig', array(
                                                                    'writeOffsIsGroup' => $writeOffsIsGroup,
                                                                    'writeOffs'        => $writeOffs
                                                                    ));
    }
}
