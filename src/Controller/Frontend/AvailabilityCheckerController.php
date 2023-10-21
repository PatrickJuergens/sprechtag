<?php

namespace App\Controller\Frontend;

use App\Repository\TeacherRepository;
use App\Repository\TimeFrameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class AvailabilityCheckerController extends AbstractController
{
    #[Route('/availability/checker/{token}', name: 'app_availability_checker')]
    public function index(string $token, Request $request, TeacherRepository $teacherRepository, TimeFrameRepository $timeFrameRepository,  EntityManagerInterface $entityManager): Response
    {
        $teacher = $teacherRepository->findOneBy(['token' => $token]);
        if ($teacher === null) {
            throw new NotFoundHttpException('Parameter nicht gefunden');
        }

        if ($request->getMethod() == 'POST') {
            $allTimeFrames = $timeFrameRepository->findAll();
            foreach ($allTimeFrames as $timeFrame) {
                $teacher->removeAvailableTimeFrame($timeFrame);
            }

            $selectedTimeFrames = $request->get('timeFrame');
            dump($selectedTimeFrames);
            foreach ($selectedTimeFrames as $name => $available) {
                $timeFrame = $timeFrameRepository->findOneBy(['name' => $name]);
                if ($timeFrame !== null) {
                    if ($available == 1) {
                        $teacher->addAvailableTimeFrame($timeFrame);
                    }
                }
            }
            $entityManager->persist($teacher);
            $entityManager->flush();
            $this->addFlash('success', "Verfügbarkeiten wurden erfasst");

        }

        return $this->render('frontend/availability_checker/index.html.twig', [
            'teacher' => $teacher,
            'timeFrames' => $timeFrameRepository->findAll()
        ]);
    }
}
