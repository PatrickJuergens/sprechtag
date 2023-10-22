<?php

namespace App\Controller\Backend;

use App\Entity\TimeFrame;
use App\Form\TimeFrameType;
use App\Repository\TimeFrameRepository;
use App\Table\TimeFrameTable;
use Doctrine\ORM\EntityManagerInterface;
use HelloSebastian\HelloBootstrapTableBundle\HelloBootstrapTableFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/sprechtagAdmin/time/frame')]
#[IsGranted("ROLE_SUPER_ADMIN")]
class TimeFrameController extends AbstractController
{
    #[Route('/', name: 'app_time_frame_index', methods: ['GET'])]
    public function index(Request $request, HelloBootstrapTableFactory $tableFactory): Response
    {
        $table = $tableFactory->create(TimeFrameTable::class);

        $table->handleRequest($request);
        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('backend/time_frame/index.html.twig', array(
            'table' => $table->createView()
        ));
    }

    #[Route('/new', name: 'app_time_frame_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $timeFrame = new TimeFrame();
        $form = $this->createForm(TimeFrameType::class, $timeFrame);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->persist($timeFrame);
                $entityManager->flush();
                $this->addFlash('success', "Das Zeitfenster wurde angelegt!");

                return $this->redirectToRoute('app_time_frame_index', [], Response::HTTP_SEE_OTHER);
            } catch (\Exception $exception) {
                $this->addFlash('error', "Das Zeitfenster konnte nicht angelegt werden: {$exception->getMessage()}");
            }
        }

        return $this->render('backend/time_frame/new.html.twig', [
            'time_frame' => $timeFrame,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_time_frame_show', methods: ['GET'])]
    public function show(TimeFrame $timeFrame): Response
    {
        return $this->render('backend/time_frame/show.html.twig', [
            'time_frame' => $timeFrame,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_time_frame_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TimeFrame $timeFrame, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TimeFrameType::class, $timeFrame);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->flush();
                $this->addFlash('success', "Das Zeitfenster wurde bearbeitet!");

                return $this->redirectToRoute('app_time_frame_index', [], Response::HTTP_SEE_OTHER);
            } catch (\Exception $exception) {
                $this->addFlash('error', "Das Zeitfenster konnte nicht bearbeitet werden: {$exception->getMessage()}");
            }
        }

        return $this->render('backend/time_frame/edit.html.twig', [
            'time_frame' => $timeFrame,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_time_frame_delete', methods: ['POST'])]
    public function delete(Request $request, TimeFrame $timeFrame, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$timeFrame->getId(), $request->request->get('_token'))) {
            try {
                $entityManager->remove($timeFrame);
                $entityManager->flush();
                $this->addFlash('success', "Das Zeitfenster wurde gelöscht!");
            } catch (\Exception $exception) {
                $this->addFlash('error', "Das Zeitfenster konnte nicht gelöscht werden: {$exception->getMessage()}");
            }
        }

        return $this->redirectToRoute('app_time_frame_index', [], Response::HTTP_SEE_OTHER);
    }
}
