<?php

namespace App\Form;

use App\Entity\MedicionGenerica;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class EdicionMedicionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fecha')
            ->add('hora')
            ->add('latitud')
            ->add('longitud')
            ->add('localizacion')
            ->add('altitud')
            ->add('temp_infrarroja')
            ->add('temp_sensor')
            ->add('observaciones', TextAreaType::class)
            ->add('Enviar', SubmitType::class)
            ->getForm();
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MedicionGenerica::class,
            'required' => false,
        ]);
    }
}
