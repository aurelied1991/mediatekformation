<?php


namespace App\Controller\admin;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Contrôleur pour la gestion des catégories côté administrateur
 * @author Aurelie Demange
 */
class AdminCategoriesController extends AbstractController
{
    /**
     * Accès aux données des formations
     * @var FormationRepository
     */
    private $formationRepository;
    
    /**
     * Accès aux données des catégories
     * @var CategorieRepository
     */
    private $categorieRepository;
    
    /**
     * Chemin de la page twig qui affiche les catégories côté administrateur
     */
    private const PAGE_CATEGORIES= 'admin/admin.categories.html.twig';
    
    /**
     * Construteur
     * @param FormationRepository $formationRepository
     * @param CategorieRepository $categorieRepository
     */
    public function __construct(FormationRepository $formationRepository, CategorieRepository $categorieRepository)
    {
        $this->formationRepository = $formationRepository;
        $this->categorieRepository = $categorieRepository;
    }
    
    /**
     * Aaffiche la liste des catégories et la possibilité d'ajouter une nouvelle catégorie via un formulaire
     * @param Request $request Requête HTTP
     * @return Response Page de gestion des catégories
     */
    #[Route('/admin/categories', name: 'admin.categories')]
    public function index(Request $request): Response
    {
        //Création d'un nouvel objet de type Categorie
        $categorie = new Categorie();
        //Création du formulaire
        $formCategorie = $this->createForm(CategorieType::class, $categorie);
        $formCategorie->handleRequest($request);
        //Gestion soumission formulaire
        if ($formCategorie->isSubmitted() && $formCategorie->isValid()) {
            //Vérifie que la catégorie n'existe pas déjà
            $categorieExiste = $this->categorieRepository->findOneByName($categorie->getName());
            if ($categorieExiste) {
                //Message pour prévenir l'utilisateur que action impossible
                $this->addFlash('danger', 'Cette catégorie existe déjà.');
            } else {
                //Ajout de la catégorie
                $this->categorieRepository->add($categorie);
                $this->addFlash('success', 'La catégorie a bien été ajoutée.');
                return $this->redirectToRoute('admin.categories');
            }
        }
        // Récupère toutes les catégories pour l'affichage
        $categories = $this->categorieRepository->findAllSorted();
        //Redirige une route après l'opération
        return $this->render(self::PAGE_CATEGORIES, [
            'categories' => $categories,
            'formCategorie' => $formCategorie->createView(),
        ]);
    }
    
    /**
     * Supprime une catégorie de la base de données si elle n'est rattachée à aucune formation
     * @param int $id Id de la catégorie à supprimer
     * @return Response Page de gestion des catégories
    */
    #[Route('/admin/categorie/delete/{id}', name: 'admin.categorie.delete')]
     public function suppr(int $id): Response
     {
        //Récupère l'objet categorie correspondant à id reçu en paramètre
        $categorie = $this->categorieRepository->find($id);
        if (count($categorie->getFormations()) === 0) {
            //Appelle la méthode 'remove' du repository
            $this->categorieRepository->remove($categorie);
            $this->addFlash('success', 'La catégorie a bien été supprimée.');
        } else {
        $this->addFlash('danger', 'Impossible de supprimer une catégorie associée à des formations.');
        }
        //Redirige une route après l'opération
        return $this->redirectToRoute('admin.categories');
    }
}
