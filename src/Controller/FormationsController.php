<?php
namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur gérant l'affichage et les actions liées aux formations
 * @author emds
 */
class FormationsController extends AbstractController
{
    /**
     * Permet d'accéder aux données des formations
     * @var FormationRepository
     */
    private $formationRepository;
    
    /**
     * Permet d'accéder aux données des catégories
     * @var CategorieRepository
     */
    private $categorieRepository;
    
    /**
     * Chemin de la page twig qui affiche la liste des formations
     */
    private const PAGE_FORMATIONS = 'pages/formations.html.twig';

    /**
     * Constructeur du contrôleur
     * @param FormationRepository $formationRepository
     * @param CategorieRepository $categorieRepository
     */
    public function __construct(FormationRepository $formationRepository, CategorieRepository $categorieRepository)
    {
        $this->formationRepository = $formationRepository;
        $this->categorieRepository= $categorieRepository;
    }
    
    /**
     * Affiche la liste de l'ensemble des formations avec leurs catégories
     * @return Response Page affichant cet ensemble
     */
    #[Route('/formations', name: 'formations')]
    public function index(): Response
    {
        $formations = $this->formationRepository->findAll();
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::PAGE_FORMATIONS, [
            'formations' => $formations,
            'categories' => $categories
        ]);
    }
    
    /**
     * Trie les formations selon un champ et un ordre spécifiés
     * @param type $champ Champ sur lequel appliquer le tri
     * @param type $ordre Ordre du tri (ASC ou DESC)
     * @param type $table Table concernée si nécessaire
     * @return Response Page affichant les formations triées
     */
    #[Route('/formations/tri/{champ}/{ordre}/{table}', name: 'formations.sort')]
    public function sort($champ, $ordre, $table=""): Response
    {
        $formations = $this->formationRepository->findAllOrderBy($champ, $ordre, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::PAGE_FORMATIONS, [
            'formations' => $formations,
            'categories' => $categories
        ]);
    }
    
    /**
     * Recherche les formations contenant une valeur donnée dans un champ donné
     * @param type $champ Champ dans lequel effectuer la recherche
     * @param Request $request Contient la valeur recherchée
     * @param type $table Table concernée si nécessaire
     * @return Response Page affichant les résultats de la recherche
     */
    #[Route('/formations/recherche/{champ}/{table}', name: 'formations.findallcontain')]
    public function findAllContain($champ, Request $request, $table=""): Response
    {
        $valeur = $request->get("recherche");
        $formations = $this->formationRepository->findByContainValue($champ, $valeur, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::PAGE_FORMATIONS, [
            'formations' => $formations,
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => $table
        ]);
    }
    
    /**
     * Affiche la page détaillée d'une formation
     * @param type $id Identifiant de la formation à afficher
     * @return Response Page contenant les détails de la formation
     */
    #[Route('/formations/formation/{id}', name: 'formations.showone')]
    public function showOne($id): Response
    {
        $formation = $this->formationRepository->find($id);
        return $this->render("pages/formation.html.twig", [
            'formation' => $formation
        ]);
    }
}
