<?php

namespace App\Controller\admin;

use App\Entity\Formation;
use App\Form\FormationType;
use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Contrôleur pour la gestion des formations côté administrateur avec affichage, tri, recherche,
 * ajout, modification et suppression des formations
 * @author Aurelie Demange
 */
class AdminFormationsController extends AbstractController
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
     * Constante contenant le chemin vers le template affichant la liste des formations
     */
    private const PAGE_FORMATIONS = 'admin/admin.formations.html.twig';
    
    /**
     * Constructeur de la classe
     * @param FormationRepository $formationRepository
     * @param CategorieRepository $categorieRepository
     */
    public function __construct(FormationRepository $formationRepository, CategorieRepository $categorieRepository)
    {
        $this->formationRepository = $formationRepository;
        $this->categorieRepository = $categorieRepository;
    }
    
    /**
     * Affiche la liste complète des formations avec leurs catégories
     * @return Response
     */
    #[Route('/admin', name: 'admin.formations')]
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
     * Permet le tri de la liste des formations selon un champ et un ordre
     * @param type $champ Champ sur lequel effectuer le tri
     * @param type $ordre ASC ou DESC
     * @param type $table Table cible pour le tri
     * @return Response
     */
    #[Route('/admin/formations/tri/{champ}/{ordre}/{table}', name: 'admin.formations.sort')]
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
     * Recherche des formations selon une valeur spécifiée dans un champ donné
     * @param type $champ Champ à rechercher
     * @param Request $request Reqûete contenant valeur à rechercher
     * @param type $table Table cible pour recherche
     * @return Response
     */
    #[Route('/admin/formations/recherche/{champ}/{table}', name: 'admin.formations.findallcontain')]
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
     * Ajoute une nouvelle formation
     * @param Request $request Requête contenant les données du formulaire
     * @return Response
     */
    #[Route('/admin/formation/ajout', name: 'admin.formation.ajout')]
    public function ajout(Request $request): Response
    {
        //crétation d'un nouvel objet de type Formation
        $formation = new Formation();
        $formFormation = $this->createForm(FormationType::class, $formation);
        $formFormation->handleRequest($request);
        if ($formFormation->isSubmitted() && $formFormation->isValid()) {
            $this->formationRepository->add($formation);
            return $this->redirectToRoute('admin.formations');
        }
        return $this->render("admin/admin.formation.ajout.html.twig", [
            'formation' => $formation,
            'formFormation' => $formFormation->createView()
        ]);
    }
    
    /**
     * Modifie les informations d'une formation existante
     * @param int $id Id de la formation à modifier
     * @param Request $request Requête contenant données du formulaire
     * @return Response
     */
    #[Route('/admin/formation/edit/{id}', name: 'admin.formation.edit')]
    public function edit(int $id, Request $request): Response
    {
        $formation = $this->formationRepository->find($id);
        //Créer un objet qui va contenir les infos du formulaire
        $formFormation = $this->createForm(FormationType::class, $formation);
        //Le formulaire tente de récupérer la requête avec handleRequest
        $formFormation->handleRequest($request);
        if ($formFormation->isSubmitted() && $formFormation->isValid()) {
            //Appel de la méthode add du repository et les modifs seront enregistrées dans la bdd
            $this->formationRepository->add($formation);
            //Redirection vers la liste des formations
            return $this->redirectToRoute('admin.formations');
        }
        return $this->render("admin/admin.formation.edit.html.twig", [
            'formation' => $formation,
            'formFormation' => $formFormation->createView()
        ]);
    }
    
    /**
     * Supprime une formation de la base de données
     * @param int $id Id de la formation à supprimer
     * @return Response
    */
    #[Route('/admin/formation/delete/{id}', name: 'admin.formation.delete')]
     public function suppr(int $id): Response
     {
        //Permet de récupérer l'objet formation correspondant à id reçu en paramètre
        $formation = $this->formationRepository->find($id);
        //Permet d'appeler la méthode 'remove' du repository
        $this->formationRepository->remove($formation);
        $this->addFlash('success', 'La formation a bien été supprimée');
        //Permet de rediriger une route après l'opération
        return $this->redirectToRoute('admin.formations');
    }
    
    /**
     * Affiche le détail d'une formation spécifique
     * @param type $id Identifiant de la formation
     * @return Response
     */
    #[Route('/admin/formations/formation/{id}', name: 'admin.formations.showone')]
    public function showOne($id): Response
    {
        //Récupérer formation correspondante à l'id
        $formation = $this->formationRepository->find($id);
        //Transmet à la vue les données de la formation sélectionnée
        return $this->render("admin/admin.formation.html.twig", [
            'formation' => $formation
        ]);
    }
    
    /**
     * Affiche la page des conditions générales d'utilisation
     * @return Response
     */
    #[Route('/admin/cgu', name: 'admin.cgu')]
    public function adminCgu(): Response
    {
    return $this->render("pages/cgu.html.twig");
    }
}
