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
 * Contrôleur de gestion des playlists : affichage, tri, recherche, ajout, modification
 * et suppression
 * @author Aurélie Demange
 */
class AdminPlaylistsController extends AbstractController
{
    /**
     * Permet d'accéder aux données des playlists
     * @var PlaylistRepository
     */
    private $playlistRepository;
    
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
     * Chemin de la page twig qui affiche les playlists côté administrateur
     */
    private const PAGE_PLAYLISTS = 'admin/admin.playlists.html.twig';
    
    /**
     * Constructeur
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
     * Permet d'afficher la liste des playlists avec leurs catégories
     * @return Response Page de gestion des playlists
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
     * Méthode qui permet de trier par ordre ASC ou DESC selon le nom
     * ou le nombre de formations par playlist
     * @param type $champ Champ à trier
     * @param type $ordre Ordre du tri
     * @return Response Page des playlists triées
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
            //Si le champ est inconnu, on retourne toute la playlist par nom croissant pour éviter erreur
            $playlists = $this->playlistRepository->findAllOrderByName('ASC');
        }
        //On récupère la liste des catégories pour l'affichage
        $categories = $this->categorieRepository->findAll();
        //On envoie à la vue les playlists avec un tri applicable
        return $this->render(self::PAGE_PLAYLISTS, [
            'playlists' => $playlists,
            'categories' => $categories
        ]);
    }
    
    /**
     * Permet de rechercher des playlists contenant une valeur spécifique
     * @param type $champ Champ à rechercher
     * @param Request $request Contient la valeur à rechercher
     * @param type $table Table concernée
     * @return Response Page des playslists avec le résultat de la recherche
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
     * Permet d'afficher le détail d'une playlist avec ses catégories et formations
     * @param type $id Identifiant de la playlist à afficher
     * @return Response Page détaillée de la playlist
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
     * Permet d'ajouter une nouvelle playlist
     * @param Request $request Requête contenant les données du formulaire
     * @return Response Page des playlists si valide ou page du formulaire si pas valide
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
     * Permet de modifier les informations de la playlist sélectionnée
     * @param int $id Id de la playlist à modifier
     * @param Request $request Requête contenant données du formulaire
     * @return Response Page des playlists si valide ou page du formulaire si pas valide
     */
    #[Route('/admin/playlist/edit/{id}', name: 'admin.playlist.edit')]
    public function edit(int $id, Request $request): Response
    {
        $playlist = $this->playlistRepository->find($id);
        //Créer un objet qui va contenir les infos du formulaire
        $formPlaylist = $this->createForm(PlaylistType::class, $playlist);
        //Le formulaire tente de récupérer la requête avec handleRequest
        $formPlaylist->handleRequest($request);
        if ($formPlaylist->isSubmitted() && $formPlaylist->isValid()) {
            //Appel de la méthode add du repository et les modifs seront enregistrées dans la bdd
            $this->playlistRepository->add($playlist);
            //Redirection vers la liste des formations
            return $this->redirectToRoute('admin.playlists');
        }
        // Récupérer les formations liées à cette playlist
        $formations = $this->formationRepository->findAllForOnePlaylist($id);

        return $this->render("admin/admin.playlist.edit.html.twig", [
            'playlist' => $playlist,
            'formations' => $formations,
            'formPlaylist' => $formPlaylist->createView()
        ]);
    }
    
    /**
     * Permet de supprimer une playlist de la base de données si elle ne contient aucune formation
     * @param int $id Id de la playlist à supprimer
     * @return Response Page de gestion des playlists
    */
    #[Route('/admin/playlist/delete/{id}', name: 'admin.playlist.delete')]
     public function suppr(int $id): Response
     {
        //Permet de récupérer l'objet playlist correspondant à id reçu en paramètr
        $playlist = $this->playlistRepository->find($id);
        //Récupérer les formations de la playlist
        $formations = $this->formationRepository->findAllForOnePlaylist($id);
        if (count($formations) === 0) {
            //Permet d'appeler la méthode 'remove' du repository
            $this->playlistRepository->remove($playlist);
            $this->addFlash('success', 'La playlist a bien été supprimée.');
        } else {
        $this->addFlash('danger', 'Impossible de supprimer une playlist contenant des formations.');
        }
        //Permet de rediriger une route après l'opération
        return $this->redirectToRoute('admin.playlists');
    }
}

