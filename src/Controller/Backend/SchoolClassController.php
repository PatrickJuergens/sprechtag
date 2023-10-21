<?php

namespace App\Controller\Backend;

use App\Entity\SchoolClass;
use App\Form\SchoolClassType;
use App\Repository\SchoolClassRepository;
use App\Table\SchoolClassTable;
use App\Table\TeacherTable;
use Doctrine\ORM\EntityManagerInterface;
use HelloSebastian\HelloBootstrapTableBundle\HelloBootstrapTableFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/sprechtagAdmin/school/class')]
#[IsGranted("ROLE_ADMIN")]
class SchoolClassController extends AbstractController
{
    #[Route('/', name: 'app_school_class_index', methods: ['GET'])]
    public function index(Request $request, HelloBootstrapTableFactory $tableFactory): Response
    {
        $table = $tableFactory->create(SchoolClassTable::class);

        $table->handleRequest($request);
        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('backend/school_class/index.html.twig', array(
            'table' => $table->createView()
        ));
    }

    #[Route('/new', name: 'app_school_class_new', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_SUPER_ADMIN")]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $schoolClass = new SchoolClass();
        $form = $this->createForm(SchoolClassType::class, $schoolClass);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($schoolClass);
            $entityManager->flush();

            return $this->redirectToRoute('app_school_class_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backend/school_class/new.html.twig', [
            'school_class' => $schoolClass,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_school_class_show', methods: ['GET'])]
    public function show(SchoolClass $schoolClass): Response
    {
        return $this->render('backend/school_class/show.html.twig', [
            'school_class' => $schoolClass,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_school_class_edit', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_SUPER_ADMIN")]
    public function edit(Request $request, SchoolClass $schoolClass, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SchoolClassType::class, $schoolClass);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_school_class_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backend/school_class/edit.html.twig', [
            'school_class' => $schoolClass,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_school_class_delete', methods: ['POST'])]
    #[IsGranted("ROLE_SUPER_ADMIN")]
    public function delete(Request $request, SchoolClass $schoolClass, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$schoolClass->getId(), $request->request->get('_token'))) {
            $entityManager->remove($schoolClass);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_school_class_index', [], Response::HTTP_SEE_OTHER);
    }
}
