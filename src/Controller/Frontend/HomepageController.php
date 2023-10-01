<?php

namespace App\Controller\Frontend;

use App\Entity\Appointment;
use App\Entity\Image;
use App\Entity\SchoolClass;
use App\Entity\Teacher;
use App\Form\AppointmentType;
use App\Form\ImageType;
use App\Repository\AppointmentRepository;
use App\Repository\ImageRepository;
use App\Repository\SchoolClassRepository;
use App\Repository\TeacherRepository;
use App\Repository\TimeFrameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Jsvrcek\ICS\CalendarExport;
use Jsvrcek\ICS\Model\CalendarEvent;
use Jsvrcek\ICS\Model\Relationship\Attendee;
use Jsvrcek\ICS\Utility\Formatter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

#[Route('/')]
class HomepageController extends AbstractController
{

    #[Route('/', name: 'app_homepage', methods: ['GET'])]
    public function index(): Response
    {
        $defaultData = ['message' => 'Type your message here'];
        $form = $this->createFormBuilder($defaultData)->add('teacher', Select2EntityType::class, [
            'class' => Teacher::class,
            'label' => 'Wählen Sie die gewünschte Lehrkraft aus.',
            'remote_route' => 'find_teacher',
            'primary_key' => 'id',
            'placeholder' => 'Nachname der Lehrkraft',
            'width' => '100%',
            'language' => 'de',
            'help' => 'Geben Sie den ersten Buchstaben des Nachnamens der gesuchten Lehrkraft ein und Sie erhalten passende Vorschläge.'
        ])->getForm();

        return $this->render('frontend/homepage/index.html.twig', ['form' => $form->createView() ]);
    }

    #[Route('/findTeacher.json', name: 'find_teacher', methods: ['GET'])]
    public function findTeacher(Request $request, TeacherRepository $repository): Response
    {
        $q = $request->get('q', null);
        if ($q === null) {
            throw new NotFoundHttpException('Parameter q nicht gefunden');
        }
        $teachers = $repository->findAvailableTeacher($q);
        $return = [];
        /** @var Teacher $teacher */
        foreach ($teachers as $teacher) {
            $return[] = [
                'id'=>$teacher->getId(),
                'text'=> $teacher->__toString(),
                'html'=>"<p>{$teacher->__toString()}</p>"];
        }
        return new JsonResponse($return);
    }

    #[Route('/findClass.json', name: 'find_class', methods: ['GET'])]
    public function findClass(Request $request, SchoolClassRepository $repository): Response
    {
        $q = $request->get('q', null);
        if ($q === null) {
            throw new NotFoundHttpException('Parameter q nicht gefunden');
        }
        $classes = $repository->findByQuery($q);
        $return = [];
        /** @var SchoolClass $teacher */
        foreach ($classes as $class) {
            $return[] = [
                'id'=>$class->getId(),
                'text'=> $class->__toString(),
                'html'=>"<p>{$class->__toString()}</p>"];
        }
        return new JsonResponse($return);
    }

    #[Route('/teacherSelectTimeFrame/', name: 'app_select_time_frame', methods: ['GET', 'POST'])]
    public function teacherSelectTime(Request $request, TeacherRepository $repository, TimeFrameRepository $timeFrameRepository, EntityManagerInterface $entityManager): Response
    {
        $formData = $request->query->all();
        $teacherId = $formData['form']['teacher'] ?? null;
        if ($teacherId === null) {
            throw new NotFoundHttpException('Parameter nicht gefunden');
        }
        $teacher = $repository->find($teacherId);
        if ($teacher === null) {
            throw new NotFoundHttpException('Lehrer wurde nicht gefunden');
        }

        if (count($this->getFreeTimeFrames($teacher,  $timeFrameRepository)) <= 0) {
            $this->addFlash('warning', "Es sind für die ausgewählte Lehrkraft sind leider keine Termine mehr frei, bitte wenden Sie sich direkt an die Lehrkraft oder kontaktieren Sie die Klassenlehrerin bzw. den Klassenlehrer!");
            return $this->redirectToRoute('app_homepage', [], Response::HTTP_SEE_OTHER);
        }

        $appointment = new Appointment();
        $appointment->setTeacher($teacher);
        $form = $this->createForm(AppointmentType::class, $appointment ,['teacher' => $teacher]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $appointment = $form->getData();
            /** @var Appointment $appointment */
            $appointment->setToken(md5(uniqid(mt_rand(), true)));
            $entityManager->persist($appointment);
            $entityManager->flush();

            return $this->redirectToRoute('app_confirm_appointment', ['token' => $appointment->getToken()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('frontend/homepage/teacherSelectTimeFrame.html.twig', ['teacher' => $teacher, 'form' => $form]);
    }

    #[Route('/confirmAppointment/{token}', name: 'app_confirm_appointment', methods: ['GET'])]
    public function confirm(String $token, AppointmentRepository $repository): Response
    {
        $appointment = $repository->findOneBy(['token' => $token]);
        if ($appointment === null) {
            throw new NotFoundHttpException('Parameter nicht gefunden');
        }

        return $this->render('frontend/homepage/confirmAppointment.html.twig', ['appointment' => $appointment]);
    }

    #[Route('/ical/{token}', name: 'app_ical_appointment', methods: ['GET'])]
    public function icalAction(string $token, Formatter $formatter, CalendarExport $calendarExport, AppointmentRepository $repository) :Response
    {
        $appointment = $repository->findOneBy(['token' => $token]);
        if ($appointment === null) {
            throw new NotFoundHttpException('Parameter nicht gefunden');
        }

        $eventOne = new CalendarEvent();
        $eventOne->setStart(new \DateTime())
            ->setSummary('Family reunion')
            ->setUid('event-uid');

        //add an Attendee
        $attendee = new Attendee($formatter);
        $attendee->setValue('moe@example.com')
            ->setName('Moe Smith');
        $eventOne->addAttendee($attendee);


        $response = new Response($calendarExport->getStream());
        $response->headers->set('Content-Type', 'text/calendar');

        return $response;
    }

    private function getFreeTimeFrames(Teacher $teacher, TimeFrameRepository $timeFrameRepository) :array
    {
        $return = [];
        $occupiedTimeFrameIds = $teacher->getOccupiedTimeFrameIds();
        foreach ($timeFrameRepository->findAll() as $timeFrame) {
            if (!in_array($timeFrame->getId(), $occupiedTimeFrameIds)) {
                $return[] = $timeFrame;
            }
        }
        return $return;
    }
}
