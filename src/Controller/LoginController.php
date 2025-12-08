<?php

namespace App\Controller;

use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Contrôleur gérant l'authentification
 */
class LoginController extends AbstractController
{
    /**
     * Affiche le formulaire de connexion et gère les erreurs d'authentification
     * @param AuthenticationUtils $authenticationUtils Fournit les infos sur la dernière tentative de connexion
     * @return Response Page de connexion avec le dernier nom d'utilisateur et l'erreur
     */
    #[Route('/login', name: 'app_login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        //Récupération de l'erreur de connexion s'il y en a
        $error = $authenticationUtils->getLastAuthenticationError();
        //Récupération du dernier nom d'utilisateur utilisé
        $lastUserName = $authenticationUtils->getLastUsername();
        return $this->render('login/index.html.twig', [
            'last_username' => $lastUserName,
            'error'           => $error
        ]);
    }
    
    /**
     * Déconnexion de l'utilisateur
     * @throws LogicException
     */
    #[Route('/logout', name: 'logout')]
    public function logout()
    {
        throw new LogicException('Cette méthode est gérée par le firewall et ne devrait jamais être appelée.');
    }
}
