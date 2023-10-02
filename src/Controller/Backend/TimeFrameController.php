<?php

namespace App\Controller\Backend;

use App\Entity\TimeFrame;
use App\Form\TimeFrameType;
use App\Repository\TimeFrameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/backend/time/frame')]
class TimeFrameController extends AbstractController
{
    #[Route('/', name: 'app_time_frame_index', methods: ['GET'])]
    public function index(TimeFrameRepository $timeFrameRepository): Response
    {
        return $this->render('backend/time_frame/index.html.twig', [
            'time_frames' => $timeFrameRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_time_frame_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $timeFrame = new TimeFrame();
        $form = $this->createForm(TimeFrameType::class, $timeFrame);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($timeFrame);
            $entityManager->flush();

            return $this->redirectToRoute('app_time_frame_index', [], Response::HTTP_SEE_OTHER);
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
            $entityManager->flush();

            return $this->redirectToRoute('app_time_frame_index', [], Response::HTTP_SEE_OTHER);
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
            $entityManager->remove($timeFrame);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_time_frame_index', [], Response::HTTP_SEE_OTHER);
    }
}
