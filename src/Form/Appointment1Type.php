<?php

namespace App\Form;

use App\Entity\Appointment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Appointment1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('visitorFirstName',TextType::class, ['label' => 'Vorname des Besuchers'])
            ->add('visitorLastName',TextType::class, ['label' => 'Nachname des Besuchers'])
            ->add('studentFirstName', TextType::class, ['label' => 'Vorname der Schülerin, Vorname des Schülers:'])
            ->add('studentLastName', TextType::class, ['label' => 'Nachname der Schülerin, Vorname des Schülers:'])
            ->add('timeFrame')
            ->add('teacher')
            ->add('schoolClass')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Appointment::class,
        ]);
    }
}
