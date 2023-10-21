<?php

namespace App\Form;

use App\Entity\Appointment;
use App\Entity\SchoolClass;
use App\Entity\Teacher;
use App\Entity\TimeFrame;
use App\Repository\SchoolClassRepository;
use App\Repository\TimeFrameRepository;
use Doctrine\ORM\QueryBuilder;
use Gregwar\CaptchaBundle\Type\CaptchaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AppointmentType extends AbstractType
{

    private TimeFrameRepository $timeFrameRepository;

    public function __construct(TimeFrameRepository $timeFrameRepository)
    {
        $this->timeFrameRepository = $timeFrameRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Teacher $teacher */
        $teacher = $options['teacher'];

        $builder
            ->add('studentFirstName', TextType::class, ['label' => 'Vorname der Schülerin, Vorname des Schülers:'])
            ->add('studentLastName', TextType::class, ['label' => 'Nachname der Schülerin, Nachname des Schülers:'])
            ->add('timeFrame', EntityType::class, [
                'class' => TimeFrame::class,
                'choices' => $this->getFreeTimeFrames($teacher),
                'choice_label' => 'name',
                'label' => 'Verfügbare Zeiten:',
                'required' => true,
                'placeholder' => 'Bitte eine Zeit auswählen'
            ])
            ->add('visitorFirstName', TextType::class, ['label' => 'Ihr Vorname:'])
            ->add('visitorLastName', TextType::class, ['label' => 'Ihr Nachname:'])
            ->add('email', EmailType::class, ['label' => 'Ihre E-Mail-Adresse', 'help'=> 'Die E-Mail-Adresse ist kein Pflichtfeld, sollten Sie eine E-Mail-Adresse angeben, senden wir Ihnen eine Bestätigung des Termins zu.'])
            ->add('schoolClass', EntityType::class, [
                'class' => SchoolClass::class,
                'query_builder' => function (SchoolClassRepository $er) use ($teacher): QueryBuilder {
                    return $er->createQueryBuilder('s')->innerJoin('s.teachers', 't', 'WITH', 't.id = :teacher')
                        ->orderBy('s.code', 'ASC')
                        ->setParameter('teacher', $teacher);
                },
                'label' => 'Wählen Sie die betreffende Schulkasse aus:',
                'help' => 'Die Angabe der Klasse ist KEIN Pflichtfeld!',
                'placeholder' => 'Bitte eine Klasse oder einen Kurs auswählen',
                'required' => false,
            ])
            ->add('captcha', CaptchaType::class, [
                'label' => 'Bitte geben Sie den Captcha-Code aus dem Bild ein:'
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'teacher' => null,
            'data_class' => Appointment::class,
        ]);
    }

    private function getFreeTimeFrames(Teacher $teacher) :array
    {
        $return = [];
        $occupiedTimeFrameIds = $teacher->getOccupiedTimeFrameIds();
        foreach ($teacher->getAvailableTimeFrames() as $timeFrame) {
            if (!in_array($timeFrame->getId(), $occupiedTimeFrameIds)) {
                $return[] = $timeFrame;
            }
        }
        return $return;
    }
}
