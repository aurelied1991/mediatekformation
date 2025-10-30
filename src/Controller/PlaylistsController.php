<?php
namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use App\Repository\PlaylistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur gérant playlists
 * Permet l'affichage de l'ensemble des playlists et le détail de chaque playlist, de faire des tris, des recherches
 * @author emds
 */
class PlaylistsController extends AbstractController
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
     * Chemin de la page twig qui affiche la liste des playlists
     */
    private const PAGE_PLAYLISTS = 'pages/playlists.html.twig';
    
    /**
     * Constructeur
     * @param PlaylistRepository $playlistRepository
     * @param CategorieRepository $categorieRepository
     * @param FormationRepository $formationRespository
     */
    public function __construct(
        PlaylistRepository $playlistRepository,
        CategorieRepository $categorieRepository,
        FormationRepository $formationRespository
    ) {
        $this->playlistRepository = $playlistRepository;
        $this->categorieRepository = $categorieRepository;
        $this->formationRepository = $formationRespository;
    }
    
    /**
     * Affiche l'ensemble des playlists avec leurs catégories
     * @Route("/playlists", name="playlists")
     * @return Response
     */
    #[Route('/playlists', name: 'playlists')]
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
     * @param type $ordre Ordre de tri
     * @return Response Page des playlists
     */
    #[Route('/playlists/tri/{champ}/{ordre}', name: 'playlists.sort')]
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
     * Permet de rechercher les playlists contenant une valeur spécifique dans un champ donné
     * @param type $champ Champ à rechercher
     * @param Request $request Contient valeur à rechercher
     * @param type $table Table concernée
     * @return Response Page avec résultat des recherches
     */
    #[Route('/playlists/recherche/{champ}/{table}', name: 'playlists.findallcontain')]
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
     * Permet d'afficher les détails d'une playlist
     * @param type $id Identifiant de la playlist à afficher
     * @return Response Page de la playlist détaillée
     */
    #[Route('/playlists/playlist/{id}', name: 'playlists.showone')]
    public function showOne($id): Response
    {
        $playlist = $this->playlistRepository->find($id);
        $playlistCategories = $this->categorieRepository->findAllForOnePlaylist($id);
        $playlistFormations = $this->formationRepository->findAllForOnePlaylist($id);
        return $this->render("pages/playlist.html.twig", [
            'playlist' => $playlist,
            'playlistcategories' => $playlistCategories,
            'playlistformations' => $playlistFormations
        ]);
    }
}
