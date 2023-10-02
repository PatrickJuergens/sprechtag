<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('/frontend/login/index.html.twig', [
            'controller_name' => 'LoginController',
            'last_username'   => $lastUsername,
            'error'           => $error,
        ]);
    }
    #[Route('/login/redirect', name: 'app_login_redirect')]
    public function loginRedirect(Security $security): Response
    {
        return $this->redirectToRoute('app_backend');
    }

    #[Route('/logout', name: 'app_logout', methods: ['GET'])]
    public function logout(): never
    {
        // controller can be blank: it will never be called!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }


}
