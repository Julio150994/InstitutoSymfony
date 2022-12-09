<?php

namespace App\Form;

use App\Entity\Alumnos;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FloatType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AlumnosFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre', TextType::class, array('data_class' => null), [
                'label' => 'Nombre',                
                'required' => true
            ])
            ->add('apellidos', TextType::class, array('data_class' => null), [
                'label' => 'Apellidos',
                'required' => true
            ])
            ->add('edad', NumberType::class, array('data_class' => null), [
                'label' => 'Edad',
                'required' => true
            ])
            ->add('foto', FileType::class, array('data_class' => null), [
                'label' => 'Foto',
                'required' => true
            ])
            ->add('preciomatricula', TextType::class, array('data_class' => null), [
                'label' => 'Precio de matrícula',
                'required' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Alumnos::class,
        ]);
    }
}
