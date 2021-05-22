<?php

namespace App\Form;

use App\Entity\ArchivoMedicion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SubirArchivoMedicionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre', FileType::class, array('label' => 'formato .txt', 'data_class' => null))
            ->add('lugar', TextType::class)
            ->add('subir', SubmitType::class, array('label' => 'Subir'))
            ->getForm();
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ArchivoMedicion::class,
        ]);
    }
}
