<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ProductType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, array(
                                        'label' => "Название",
                                        'attr' => array(
                                                'class' => 'form-control')))
            ->add('cost', null, array(
                                        'label' => "Стоимость",
                                        'attr' => array(
                                                'class' => 'form-control')))
             ->add('category', EntityType::class, array(
                                                        'class' => 'AppBundle:Category',
                                                        'choice_label' => 'title',
                                                        'label' => "Выберите категорию",
                                                        'attr' => array(
                                                            'class' => 'form-control')
                                                    ))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Product'
        ));
    }
}
