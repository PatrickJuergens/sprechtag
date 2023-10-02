<?php

namespace App\Controller\Backend;

use App\Entity\Image;
use App\Form\ImageType;
use App\Repository\AppointmentRepository;
use App\Repository\ImageRepository;
use App\Repository\SchoolClassRepository;
use App\Repository\TeacherRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Route('/sprechtagAdmin')]
class HomepageController extends AbstractController
{

    #[Route('/', name: 'app_backend', methods: ['GET'])]
    public function index(TeacherRepository $teacherRepository, SchoolClassRepository $schoolClassRepository, AppointmentRepository $appointmentRepository): Response
    {
        return $this->render('backend/homepage/index.html.twig', [
            'teacherCount' => $teacherRepository->count([]),
            'schoolClassCount' => $schoolClassRepository->count([]),
            'appointmentCount' => $appointmentRepository->count([]),
        ]);
    }

}
