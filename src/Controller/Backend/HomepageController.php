<?php

namespace App\Controller\Backend;

use App\Entity\Image;
use App\Form\ImageType;
use App\Repository\ImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Route('/backend')]
class HomepageController extends AbstractController
{

    #[Route('/', name: 'app_backend', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('backend/homepage/index.html.twig', []);
    }

}
