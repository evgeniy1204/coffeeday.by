<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ReceptType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('products', EntityType::class, array(
                'class' => 'AppBundle:Product',
                'choice_label' => 'name',
                'label' => "Продукт в меню",
                'attr' => array(
                                                'class' => 'form-control')))
            ->add('ingredient', EntityType::class, array(
                'class' => 'AppBundle:Ingredient',
                'choice_label' => 'name',
                'label' => "Ингредиент",
                'attr' => array(
                                                'class' => 'form-control')))
            ->add('count', null, array(
                                        'label' => "Количество",
                                        'attr' => array(
                                                'class' => 'form-control',
                                                'min' => 0)))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Recept',
            'csrf_protection'   => false,
        ));
    }
}
