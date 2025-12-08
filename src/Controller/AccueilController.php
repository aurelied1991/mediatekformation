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
     * Repository permettant d'accéder aux données des formations
     * @var FormationRepository
     */
    private $repository;
    
    /**
     * Constructeur du contrôleur
     * @param FormationRepository $repository Repository des formations
     */
    public function __construct(FormationRepository $repository)
    {
        $this->repository = $repository;
    }
    
    /**
     * Affiche la page d'accueil avec les deux dernières formations publiées
     * @return Response Page d'accueil
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
     * @return Response Page des CGU
     */
    #[Route('/cgu', name: 'cgu')]
    public function cgu(): Response
    {
        return $this->render("pages/cgu.html.twig");
    }
}
