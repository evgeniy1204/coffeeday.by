<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class StockType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('ingredient', EntityType::class, array(
                                                    'class' => 'AppBundle:Ingredient',
                                                    'choice_label' => 'name',
                                                    'label' => "Название",
                                                    'attr' => array(
                                                                    'class' => 'form-control')
            ))
            ->add('count', null, array(
                                        'label' => "Количество",
                                        'attr' => array(
                                                'class' => 'form-control',
                                                'min' => 0)
            ));
            
            
        
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Stock'
        ));
    }
}
