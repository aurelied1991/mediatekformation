<?php
namespace App\Controller;

use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur de la page d'accueil du site
 * @author emds
 */

class AccueilController extends AbstractController
{
    
    /**
     * Permet d'accéder aux données des formations
     * @var FormationRepository
     */
    private $repository;
    
    /**
     * Constructeur
     * @param FormationRepository $repository
     */
    public function __construct(FormationRepository $repository)
    {
        $this->repository = $repository;
    }
    
    /**
     * Affichage de la page avec les deux dernières formations publiées
     * @return Response Contient la page d'accueil
     */
    #[Route('/', name: 'accueil')]
    public function index(): Response
    {
        $formations = $this->repository->findAllLasted(2);
        return $this->render("pages/accueil.html.twig", [
            'formations' => $formations
        ]);
    }
    
    /**
     * Affiche la page des conditions générales d'utilisation
     * @return Response Contient la page CGU
     */
    #[Route('/cgu', name: 'cgu')]
    public function cgu(): Response
    {
        return $this->render("pages/cgu.html.twig");
    }
}
