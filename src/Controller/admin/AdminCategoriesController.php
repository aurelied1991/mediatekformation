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
 * Contrôleur pour la gestion des catégories, côté administrateur
 * @author Aurelie Demange
 */
class AdminCategoriesController extends AbstractController
{
    /**
     * Repository pour accéder aux données des formations
     * @var FormationRepository
     */
    private $formationRepository;
    
    /**
     * Repository pour accéder aux données des catégories
     * @var CategorieRepository
     */
    private $categorieRepository;
    
    /**
     * Constante contenant le chemin vers le template affichant la liste des catégories
     */
    private const PAGE_CATEGORIES= 'admin/admin.categories.html.twig';
    
    /**
     * Construteur de la classe
     * @param FormationRepository $formationRepository
     * @param CategorieRepository $categorieRepository
     */
    public function __construct(FormationRepository $formationRepository, CategorieRepository $categorieRepository)
    {
        $this->formationRepository = $formationRepository;
        $this->categorieRepository = $categorieRepository;
    }
    
    /**
     * Affiche la liste des catégories et la possibilité d'ajouter une nouvelle catégorie via un formulaire
     * @param Request $request Requête HTTP
     * @return Response
     */
    #[Route('/admin/categories', name: 'admin.categories')]
    public function index(Request $request): Response
    {
        $categorie = new Categorie();
        //Création du formulaire
        $formCategorie = $this->createForm(CategorieType::class, $categorie);
        $formCategorie->handleRequest($request);
        if ($formCategorie->isSubmitted() && $formCategorie->isValid()) {
            $categorieExiste = $this->categorieRepository->findOneByName($categorie->getName());
            if ($categorieExiste) {
                $this->addFlash('danger', 'Cette catégorie existe déjà.');
            } else {
                $this->categorieRepository->add($categorie);
                $this->addFlash('success', 'La catégorie a bien été ajoutée.');
                return $this->redirectToRoute('admin.categories');
            }
        }
        $categories = $this->categorieRepository->findAllSorted();
        return $this->render(self::PAGE_CATEGORIES, [
            'categories' => $categories,
            'formCategorie' => $formCategorie->createView(),
        ]);
    }
    
    /**
     * Supprime une catégorie de la base de données si elle n'est rattachée à aucune formation
     * @param int $id Id de la catégorie à supprimer
     * @return Response
    */
    #[Route('/admin/categorie/delete/{id}', name: 'admin.categorie.delete')]
     public function suppr(int $id): Response
     {
        $categorie = $this->categorieRepository->find($id);
        if (count($categorie->getFormations()) === 0) {
            $this->categorieRepository->remove($categorie);
            $this->addFlash('success', 'La catégorie a bien été supprimée.');
        } else {
        $this->addFlash('danger', 'Impossible de supprimer une catégorie associée à des formations.');
        }
        return $this->redirectToRoute('admin.categories');
    }
}
