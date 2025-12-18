<?php

namespace App\Controller\admin;

use App\Entity\Playlist;
use App\Form\PlaylistType;
use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use App\Repository\PlaylistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur pour la gestion des playlists côté administrateur : affichage, tri, recherche, ajout, modification
 * et suppression
 * @author Aurelie Demange
 */
class AdminPlaylistsController extends AbstractController
{
    /**
     * Repository pour accéder aux données des playlists
     * @var PlaylistRepository
     */
    private $playlistRepository;
    
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
     * Constante contenant le chemin vers le template affichant la liste des playlists
     */
    private const PAGE_PLAYLISTS = 'admin/admin.playlists.html.twig';
    
    /**
     * Constructeur du contrôleur avec l'initialisation des trois repository
     * @param PlaylistRepository $playlistRepository
     * @param FormationRepository $formationRepository
     * @param CategorieRepository $categorieRepository
     */
    public function __construct(
        PlaylistRepository $playlistRepository,
        FormationRepository $formationRepository,
        CategorieRepository $categorieRepository
    ) {
        $this->playlistRepository = $playlistRepository;
        $this->formationRepository = $formationRepository;
        $this->categorieRepository = $categorieRepository;
    }
    
    /**
     * Affiche la liste des playlists avec leurs catégories
     * @return Response
     */
    #[Route('/admin/playlists', name: 'admin.playlists')]
    public function index(): Response
    {
        $playlists = $this->playlistRepository->findAllOrderByName('ASC');
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::PAGE_PLAYLISTS, [
            'playlists' => $playlists,
            'categories' => $categories
        ]);
    }
    
    /**
     * Permet de trier les playlists selon un champ et un ordre
     * @param type $champ Champ à trier
     * @param type $ordre Ordre du tri (ASC ou DESC)
     * @return Response
     */
    #[Route('/admin/playlists/tri/{champ}/{ordre}', name: 'admin.playlists.sort')]
    public function sort($champ, $ordre): Response
    {
        if ($champ === 'name') {
            //Si tri est demandé sur le nom des playslists, appel de la méthode findAllOrderByName
            $playlists = $this->playlistRepository->findAllOrderByName($ordre);
        } elseif ($champ === 'nbFormations') {
            //Si tri est demandé sur nombre de formations, appel de la méthode concernée du Repository
            $playlists = $this->playlistRepository->findAllOrderByNbFormations($ordre);
        } else {
            //Si le champ est inconnu, tri par défaut pour éviter les erreurs
            $playlists = $this->playlistRepository->findAllOrderByName('ASC');
        }
        //Récupération de la liste des catégories pour l'affichage
        $categories = $this->categorieRepository->findAll();
        //Affichage de la page avec les playlists triées
        return $this->render(self::PAGE_PLAYLISTS, [
            'playlists' => $playlists,
            'categories' => $categories
        ]);
    }
    
    /**
     * Permet de rechercher des playlists contenant une valeur saisie spécifique
     * @param type $champ Champ à rechercher
     * @param Request $request Valeur à rechercher
     * @param type $table Table concernée
     * @return Response
     */
    #[Route('/admin/playlists/recherche/{champ}/{table}', name: 'admin.playlists.findallcontain')]
    public function findAllContain($champ, Request $request, $table=""): Response
    {
        $valeur = $request->get("recherche");
        $playlists = $this->playlistRepository->findByContainValue($champ, $valeur, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::PAGE_PLAYLISTS, [
            'playlists' => $playlists,
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => $table
        ]);
    }
    
    /**
     * Affiche le détail d'une playlist avec ses catégories et formations
     * @param type $id Identifiant de la playlist à afficher
     * @return Response
     */
    #[Route('/admin/playlists/playlist/{id}', name: 'admin.playlists.showone')]
    public function showOne($id): Response
    {
        $playlist = $this->playlistRepository->find($id);
        $playlistCategories = $this->categorieRepository->findAllForOnePlaylist($id);
        $playlistFormations = $this->formationRepository->findAllForOnePlaylist($id);
        return $this->render("admin/admin.playlist.html.twig", [
            'playlist' => $playlist,
            'playlistcategories' => $playlistCategories,
            'playlistformations' => $playlistFormations
        ]);
    }
    
    /**
     * Permet l'ajout d'une nouvelle playlist
     * @param Request $request Requête contenant les données du formulaire
     * @return Response
     */
    #[Route('/admin/playlist/ajout', name: 'admin.playlist.ajout')]
    public function ajout(Request $request): Response
    {
        //crétation d'un nouvel objet de type Playlist
        $playlist = new Playlist();
        $formPlaylist = $this->createForm(PlaylistType::class, $playlist);
        $formPlaylist->handleRequest($request);
        if ($formPlaylist->isSubmitted() && $formPlaylist->isValid()) {
            $this->playlistRepository->add($playlist);
            return $this->redirectToRoute('admin.playlists');
        }
        return $this->render("admin/admin.playlist.ajout.html.twig", [
            'playlist' => $playlist,
            'formPlaylist' => $formPlaylist->createView()
        ]);
    }
    
    /**
     * Permet la modification d'une playlist existante
     * @param int $id Id de la playlist à modifier
     * @param Request $request Requête contenant données du formulaire
     * @return Response
     */
    #[Route('/admin/playlist/edit/{id}', name: 'admin.playlist.edit')]
    public function edit(int $id, Request $request): Response
    {
        //Récupération de la playlist à modifier
        $playlist = $this->playlistRepository->find($id);
        //Création du formulaire pré-rempli avec les données existantes de la playlist
        $formPlaylist = $this->createForm(PlaylistType::class, $playlist);
        //Récupération des données saisies
        $formPlaylist->handleRequest($request);
        //Vérifie si le formulaire est soumis et valide
        if ($formPlaylist->isSubmitted() && $formPlaylist->isValid()) {
            //Appel de la méthode add du repository et les modifications seront enregistrées dans la bdd
            $this->playlistRepository->add($playlist);
            //Redirection vers la liste des playlists
            return $this->redirectToRoute('admin.playlists');
        }
        // Récupération des formations liées à cette playlist
        $formations = $this->formationRepository->findAllForOnePlaylist($id);

        return $this->render("admin/admin.playlist.edit.html.twig", [
            'playlist' => $playlist,
            'formations' => $formations,
            'formPlaylist' => $formPlaylist->createView()
        ]);
    }
    
    /**
     * Supprime une playlist de la base de données si elle ne contient aucune formation
     * @param int $id Id de la playlist à supprimer
     * @return Response
    */
    #[Route('/admin/playlist/delete/{id}', name: 'admin.playlist.delete')]
     public function suppr(int $id): Response
     {
        //Récupération de la playlist correspondante à id reçu en paramètre
        $playlist = $this->playlistRepository->find($id);
        //Récupération des formations associées à la playlist
        $formations = $this->formationRepository->findAllForOnePlaylist($id);
        //Vérifie si la playlist ne contient aucune formation
        if (count($formations) === 0) {
            //Suppression de la playlist
            $this->playlistRepository->remove($playlist);
            $this->addFlash('success', 'La playlist a bien été supprimée.');
        } else {
        //Message d'erreur si la playlist contient des formations
        $this->addFlash('danger', 'Impossible de supprimer une playlist contenant des formations.');
        }
        return $this->redirectToRoute('admin.playlists');
    }
}
