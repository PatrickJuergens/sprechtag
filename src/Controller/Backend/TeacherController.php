<?php

namespace App\Controller\Backend;

use App\Entity\Teacher;
use App\Form\TeacherType;
use App\Repository\TeacherRepository;
use App\Service\ExcelService;
use App\Table\TeacherTable;
use Doctrine\ORM\EntityManagerInterface;
use HelloSebastian\HelloBootstrapTableBundle\HelloBootstrapTableFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Constraints\File;

#[Route('/sprechtagAdmin/teacher')]
#[IsGranted("ROLE_ADMIN")]
class TeacherController extends AbstractController
{
    #[Route('/', name: 'app_teacher_index', methods: ['GET'])]
    public function index(Request $request, HelloBootstrapTableFactory $tableFactory): Response
    {
        $table = $tableFactory->create(TeacherTable::class);

        $table->handleRequest($request);
        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('backend/teacher/index.html.twig', array(
            'table' => $table->createView()
        ));
    }

    #[Route('/new', name: 'app_teacher_new', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_SUPER_ADMIN")]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $teacher = new Teacher();
        $form = $this->createForm(TeacherType::class, $teacher);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($teacher);
            $entityManager->flush();

            return $this->redirectToRoute('app_teacher_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backend/teacher/new.html.twig', [
            'teacher' => $teacher,
            'form' => $form,
        ]);
    }

    #[Route('/import', name: 'app_teacher_import', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_SUPER_ADMIN")]
    public function import(Request $request, EntityManagerInterface $entityManager, ExcelService $excelService): Response
    {
        $defaultData = ['message' => 'Type your message here'];
        $form = $this->createFormBuilder($defaultData)
            ->add('excelFile', FileType::class,
                [
                    'label' => 'Excel-Datei',
                    'required' => true,
                    'constraints' => [
                        new File([
                            'maxSize' => '10M',
                            'mimeTypes' => [
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            ],
                            'mimeTypesMessage' => 'Bitte laden Sie eine Excel-Datei < 10 MegaByte hoch!',
                        ])
                    ],
                ])
            ->add('send', SubmitType::class, ['label' => 'Hochladen'])
            ->getForm();
        ;
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('excelFile')->getData();
            try {
                $excelService->import($file);
                $this->addFlash('success', 'Excel-Datei wurde importiert');
            } catch (\Exception $exception) {
                $this->addFlash('error', 'Excel-Datei konnte nicht importiert werden: '. $exception->getMessage());
            }

            return $this->redirectToRoute('app_teacher_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backend/teacher/import.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_teacher_show', methods: ['GET'])]
    public function show(Teacher $teacher): Response
    {
        return $this->render('backend/teacher/show.html.twig', [
            'teacher' => $teacher,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_teacher_edit', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_SUPER_ADMIN")]
    public function edit(Request $request, Teacher $teacher, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TeacherType::class, $teacher);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_teacher_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backend/teacher/edit.html.twig', [
            'teacher' => $teacher,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_teacher_delete', methods: ['POST'])]
    #[IsGranted("ROLE_SUPER_ADMIN")]
    public function delete(Request $request, Teacher $teacher, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$teacher->getId(), $request->request->get('_token'))) {
            $entityManager->remove($teacher);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_teacher_index', [], Response::HTTP_SEE_OTHER);
    }

}
