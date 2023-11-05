<?php

namespace App\Controller\Backend;

use App\Entity\Appointment;
use App\Entity\Teacher;
use App\Form\Appointment1Type;
use App\Service\WordService;
use App\Table\AppointmentTable;
use Doctrine\ORM\EntityManagerInterface;
use HelloSebastian\HelloBootstrapTableBundle\HelloBootstrapTableFactory;
use PhpOffice\PhpWord\Exception\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/sprechtagAdmin/appointment')]
#[IsGranted("ROLE_ADMIN")]
class AppointmentController extends AbstractController
{
    #[Route('/', name: 'app_appointment_index', methods: ['GET'])]
    public function index(Request $request, HelloBootstrapTableFactory $tableFactory): Response
    {
        $table = $tableFactory->create(AppointmentTable::class);
        $table->handleRequest($request);
        if ($table->isCallback()) {

            return $table->getResponse();
        }

        return $this->render('backend/appointment/index.html.twig', array(
            'table' => $table->createView()
        ));
    }

    #[Route('/exportTeacher/{id}', name: 'app_appointment_export_teacher', methods: ['GET'])]
    #[IsGranted("ROLE_USER")]
    public function exportTeacher(Teacher $teacher, WordService $wordService): Response
    {
        try {
            return $this->handleExport($wordService, [$teacher]);

        } catch (\Exception $exception) {

            $this->addFlash('error', "Die Word-Date konnte nicht erstellt werden!");
            return $this->redirectToRoute('app_teacher_show', ['id' => $teacher->getId()], Response::HTTP_SEE_OTHER);
        }
    }

    #[Route('/export', name: 'app_appointment_export', methods: ['GET'])]
    #[IsGranted("ROLE_SUPER_ADMIN")]
    public function export(Request $request, WordService $wordService): Response
    {
        try {

            return $this->handleExport($wordService);
        } catch (\Exception $exception) {
            $this->addFlash('error', "Die Word-Date konnte nicht erstellt werden!");

            return $this->redirectToRoute('app_appointment_index', [], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     * @throws Exception
     */
    private function handleExport(WordService $wordService, ?array $teacher = null): Response
    {
        $path = $wordService->export($teacher);
        $content = file_get_contents($path);
        $response = new Response();
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        $response->headers->set('Content-Disposition', 'attachment;filename="Export.docx"');
        $response->setContent($content);

        return $response;
    }


    #[Route('/new', name: 'app_appointment_new', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_SUPER_ADMIN")]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $appointment = new Appointment();
        $form = $this->createForm(Appointment1Type::class, $appointment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $appointment->setToken(md5(uniqid(mt_rand(), true)));
                $entityManager->persist($appointment);
                $entityManager->flush();
                $this->addFlash('success', "Der Termin wurde angelegt!");

                return $this->redirectToRoute('app_appointment_index', [], Response::HTTP_SEE_OTHER);
            } catch (\Exception $exception) {
                $this->addFlash('error', "Der Termin konnte nicht angelegt werden: {$exception->getMessage()}");
            }
        }

        return $this->render('backend/appointment/new.html.twig', [
            'appointment' => $appointment,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_appointment_show', methods: ['GET'])]
    public function show(Appointment $appointment): Response
    {
        return $this->render('backend/appointment/show.html.twig', [
            'appointment' => $appointment,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_appointment_edit', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_SUPER_ADMIN")]
    public function edit(Request $request, Appointment $appointment, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Appointment1Type::class, $appointment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->flush();
                $this->addFlash('success', "Der Termin wurde bearbeitet!");

                return $this->redirectToRoute('app_appointment_index', [], Response::HTTP_SEE_OTHER);
            } catch (\Exception $exception) {
                $this->addFlash('error', "Der Termin konnte nicht bearbeitet werden: {$exception->getMessage()}");
            }
        }

        return $this->render('backend/appointment/edit.html.twig', [
            'appointment' => $appointment,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_appointment_delete', methods: ['POST'])]
    #[IsGranted("ROLE_SUPER_ADMIN")]
    public function delete(Request $request, Appointment $appointment, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$appointment->getId(), $request->request->get('_token'))) {
            try {
                $entityManager->remove($appointment);
                $entityManager->flush();

                $this->addFlash('success', "Der Termin wurde gelöscht!");
            } catch (\Exception $exception) {
                $this->addFlash('error', "Der Termin konnte nicht gelöscht werden: {$exception->getMessage()}");
            }
        }

        return $this->redirectToRoute('app_appointment_index', [], Response::HTTP_SEE_OTHER);
    }

}
