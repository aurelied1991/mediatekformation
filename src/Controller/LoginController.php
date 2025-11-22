<?php

namespace App\Controller;

use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        //Récupération de l'erreur s'il y en a
        $error = $authenticationUtils->getLastAuthenticationError();
        //Récupération du dernier nom de login utilisé
        $lastUserName = $authenticationUtils->getLastUsername();
        return $this->render('login/index.html.twig', [
            'last_username' => $lastUserName,
            'error'           => $error
        ]);
    }
    
    #[Route('/logout', name: 'logout')]
    public function logout()
    {
        throw new LogicException('Cette méthode est gérée par le firewall et ne devrait jamais être appelée.');
    }
}
