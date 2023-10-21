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
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/sprechtagAdmin')]
#[IsGranted("ROLE_ADMIN")]
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

    #[Route('/password/change', name: 'app_backend_password_change', methods: ['GET', 'POST'])]
    public function changePassword(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, UserInterface $user): Response
    {
        $form = $this->createFormBuilder()
            ->add('oldPassword', PasswordType::class, ['label' => 'Altes Passwort'])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => ['label' => 'Neues Passwort'],
                'second_options' => ['label' => 'Wiederholen des neuen Passworts'],
            ])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            if (!$passwordHasher->isPasswordValid($user, $data['oldPassword'])) {
                $this->addFlash('error', "Das alte Passwort ist nicht korrekt!");

                return $this->redirectToRoute('app_backend_password_change', [], Response::HTTP_SEE_OTHER);
            }
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $data['password']
            );
            $user->setPassword($hashedPassword);
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', "Das Passwort wurde geändert!");

            return $this->redirectToRoute('app_backend', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('backend/changePassword/index.html.twig', ['form' => $form->createView()]);
    }

}
