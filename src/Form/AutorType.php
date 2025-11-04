<?php

namespace App\Form;

use App\Entity\Autor;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AutorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre', TextType::class, [
                'label' => 'Nombre del autor',
            ])
            ->add('fechaNacimiento', DateType::class, [
                'label' => 'Fecha de nacimiento',
                'widget' => 'single_text', // muestra un date picker
                'html5' => true,
            ])
            ->add('genero', ChoiceType::class, [
                'label' => 'Género',
                'choices' => [
                    'Masculino' => 'Masculino',
                    'Femenino' => 'Femenino',
                    'Otro' => 'Otro',
                ],
                'placeholder' => 'Selecciona una opción',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Autor::class,
        ]);
    }
}
