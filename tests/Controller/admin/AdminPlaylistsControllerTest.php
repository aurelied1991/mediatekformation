<?php


namespace App\tests\Controller\admin;

use App\Entity\Playlist;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;


/**
 * Tests fonctionnels du contrôleur AdminPlaylistsController
 * Contrôle l'accès aux pages, les tris et filtres sur les playlists, côté administreur
 * @author Aurelie Demange
 */
class AdminPlaylistsControllerTest extends WebTestCase
{

    private const URL_ADMIN_PLAYLISTS = '/admin/playlists';
    private const TITRE1_PLAYLIST = 'Bases de la programmation (C#)';
    private const CSS_TEXT_INFO = '.text-info';
    private const CSS_PREMIERE_COLONNE = 'td:nth-child(1) h5';
    
    /**
     * Permet de se connecter en tant qu'administrateur
     * @return type
     */
    private function loginAdmin()
    {
        $client = static::createClient();
        $user = self::getContainer()->get(UserRepository::class)
            ->findOneBy(['username' => 'admin']);
        $client->loginUser($user);
        $client->request('GET', '/admin');
        $this->assertResponseIsSuccessful("La page admin n'est pas accessible après login");
        $this->assertSelectorTextContains('h3', 'Gestion des formations', "Le titre de la page admin est incorrect");
        return $client;
    }
    
    /**
     * Permet de tester le tri des playlists par leur titre (ordre ASC) en testant
     * le résultat de la première ligne
     */
    public function testTriPlaylistsNameAsc()
    {
        $client = $this->loginAdmin();
        $crawler = $client->request('GET', 'admin/playlists/tri/name/asc');
        $this->assertResponseStatusCodeSame(
            Response::HTTP_OK,
            "Le tri ASC par nombre de formations a échoué (page inaccessible)"
        );
        $first = $crawler->filter(self::CSS_TEXT_INFO)->first()->text();
        $this->assertEquals(
            self::TITRE1_PLAYLIST,
            $first,
            "Le nom de la première playlist triée par ordre ASC est incorrect"
        );
    }
    
    /**
     * Permet de tester le tri des playlists par leur titre (ordre DESC) en testant
     * le résultat de la première ligne
     */
    public function testTriPlaylistsNameDesc()
    {
        $client = $this->loginAdmin();
        $crawler = $client->request('GET', 'admin/playlists/tri/name/desc');
        $this->assertResponseStatusCodeSame(
            Response::HTTP_OK,
            "Le tri ASC par nom des playlists a échoué (page inaccessible)"
        );
        $first = $crawler->filter(self::CSS_TEXT_INFO)->first()->text();
        $this->assertEquals(
            'Visual Studio 2019 et C#',
            $first,
            "Le nom de la première playlist triée par ordre DESC est incorrect"
        );
    }
    
    /**
     * Permet de tester la recherche des playlists par mots-clés avec le comptage du nombre
     * de lignes obtenues ainsi que le titre de la première ligne
     */
    public function testRecherchePlaylist()
    {
        $client = $this->loginAdmin();
        $client->request('GET', 'admin/playlists/recherche/name');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK, "La page de recherche par nom est inaccessible");
        $crawler = $client->submitForm('Filtrer', ['recherche' => 'C#'
        ]);
        $this->assertCount(2, $crawler->filter('h5'), "Le nombre de playlists retourné par la recherche est incorrect");
        $this->assertSelectorTextContains('h5', 'C#', "Aucune playlist contenant 'C#' n'a été trouvée");
        $first = $crawler->filter(self::CSS_TEXT_INFO)->first()->text();
        $this->assertEquals(self::TITRE1_PLAYLIST, $first, "Le premier résultat de la recherche par nom est incorrect");
    }
    
    /**
     * Permet de tester le tri des playlists par nombre de formations de chaque playlist (ordre ASC)
     * en contrôlant le nom et le nombre de formation de la playlist de la première ligne
     */
    public function testTriPlaylistsNbFormationsAsc()
    {
        $client = $this->loginAdmin();
        $crawler = $client->request('GET', 'admin/playlists/tri/nbFormations/asc');
        $this->assertResponseStatusCodeSame(
            Response::HTTP_OK,
            "Le tri ASC par nombre de formations a échoué (page inaccessible)"
        );
        $firstNumber = intval($crawler->filter('td:nth-child(2)')->first()->text());
        $this->assertEquals(
            0,
            $firstNumber,
            "Le nombre de formations pour la première playlist triée par ordre ASC est incorrect"
        );
        $firstPlaylist = $crawler->filter(self::CSS_PREMIERE_COLONNE)->first()->text();
        $this->assertEquals(
            'playlist test',
            $firstPlaylist,
            "Le nom de la première playlist triée par ordre ASC est incorrect"
        );
    }
    
    /**
     * Permet de tester le tri des playlists par nombre de formations de chaque playlist (ordre DESC)
     * en contrôlant le nom et le nombre de formation de la playlist de la première ligne
     */
    public function testTriPlaylistsNbFormationsDesc()
    {
        $client = $this->loginAdmin();
        $crawler = $client->request('GET', 'admin/playlists/tri/nbFormations/desc');
        $this->assertResponseStatusCodeSame(
            Response::HTTP_OK,
            "Le tri DESC par nombre de formations a échoué (page inaccessible)"
        );
        $firstNumber = intval($crawler->filter('td:nth-child(2)')->first()->text());
        $this->assertEquals(
            74,
            $firstNumber,
            "Le nombre de formations pour la première playlist triée par ordre DESC est incorrect"
        );
        $firstPlaylist = $crawler->filter(self::CSS_PREMIERE_COLONNE)->first()->text();
        $this->assertEquals(
            self::TITRE1_PLAYLIST,
            $firstPlaylist,
            "Le nom de la première playlist triée par ordre DESC est incorrect"
        );
    }
    
    /**
     * Permet de tester tri des playlists selon le filtrage par catégorie, avec le comptage du nombre de
     * lignes obtenues ainsi que le titre de la première ligne
     */
    public function testFiltrePlaylistCategorie()
    {
        $client = $this->loginAdmin();
        $crawler = $client->request('POST', 'admin/playlists/recherche/id/categories', ['recherche' => 3]);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK, "La page de filtrage par catégorie est inaccessible");
        $nombreResultats = $crawler->filter('tbody tr')->count();
        $expectedCount = 2;
        $this->assertEquals(
            $expectedCount,
            $nombreResultats,
            "Le nombre de playlists retourné par le filtrage par catégorie est incorrect"
        );
        $firstPlaylistName = $crawler->filter(self::CSS_PREMIERE_COLONNE)->first()->text();
        $this->assertStringContainsString(
            self::TITRE1_PLAYLIST,
            $firstPlaylistName,
            "Le nom de la première playlist filtrée par catégorie est incorrect"
        );
    }
    
    /**
     * Permet de tester l'accès à la page de détails d'une playlist lors du clic sur la bouton 'Voir détail'
     * ainsi que le titre de la cette page
     */
    public function testAccesDetailsPlaylistFormation()
    {
        $client = $this->loginAdmin();
        $crawler = $client->request('GET', self::URL_ADMIN_PLAYLISTS);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK, "La page admin des playlists n'est pas accessible");
        $link = $crawler->selectLink('Voir détail')->link();
        $crawler = $client->click($link);
        $this->assertResponseStatusCodeSame(
            Response::HTTP_OK,
            "La page de détails de la playlist n'est pas accessible"
        );
        $uri = $client->getRequest()->server->get("REQUEST_URI");
        $this->assertEquals(
            '/admin/playlists/playlist/13',
            $uri,
            "L'URL de la page des détails de la playlist est incorrecte"
        );
        $titre = $crawler->filter('h4.text-info')->text();
        $this->assertEquals(
            self::TITRE1_PLAYLIST,
            $titre,
            "Le titre de la page des détails de la playlist est incorrect"
        );
    }
    
    /**
     * Permet de tester l'accès à la page et au formulaire d'édition d'une playlist
     */
    public function testAccesPageEditerPlaylist()
    {
        $client = $this->loginAdmin();
        $crawler = $client->request('GET', self::URL_ADMIN_PLAYLISTS);
        $link = $crawler->selectLink('Editer')->first()->link();
        $client->click($link);
        $this->assertResponseStatusCodeSame(200, "La page d'édition de playlist est inaccessible");
        $this->assertSelectorExists('form', "Le formulaire d'édition est introuvable");
        // Vérifier que le titre exact est présent
        $this->assertSelectorTextContains(
            'h2.text-center',
            "Modification d'une playlist",
            "Le titre du formulaire d'édition est incorrect"
        );
    }
    
    /**
     * Permet de tester l'échec de la suppression d'une playlist liée à au moins une formation
     */
    public function testEchecSuppressionPlaylist()
    {
        $client = $this->loginAdmin();
        $crawler = $client->request('GET', self::URL_ADMIN_PLAYLISTS);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK, "La page playlists admin est inaccessible");
        $deleteLink =  $crawler->filter('a.btn-danger')->first()->link();
        $this->assertStringContainsString(
            '/admin/playlist/delete/',
            $deleteLink->getUri(),
            "Le lien de suppression est incorrect"
        );
        $client->click($deleteLink);
        $this->assertResponseRedirects(self::URL_ADMIN_PLAYLISTS, "La redirection après suppression a échoué");
        $client->followRedirect();
        $this->assertResponseIsSuccessful("La page après la redirection n'est pas accessible");
        $this->assertSelectorExists('.alert-danger', "Le message d'échec de suppression est absent");
        $this->assertSelectorTextContains(
            'h3',
            "Gestion des playlists",
            "Le titre de la page après l'échec de la suppression est incorrect"
        );
    }
    
    /**
     * Permet de tester la réussite de la suppression d'une playlist non liée à des formations
     */
    public function testReussiteSuppressionPlaylist()
    {
        $client = $this->loginAdmin();
        $playlistTest = new Playlist();
        $playlistTest->setName('Aucune formation test');
        $entityManager = self::getContainer()->get('doctrine')->getManager();
        $entityManager->persist($playlistTest);
        $entityManager->flush();
        $crawler = $client->request('GET', self::URL_ADMIN_PLAYLISTS);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK, "La page playlists admin est inaccessible");
        $deleteLink = $crawler->filter('a.btn-danger')->first()->link();
        $this->assertStringContainsString(
            '/admin/playlist/delete/',
            $deleteLink->getUri(),
            "Le lien de suppression est incorrect"
        );
        $client->click($deleteLink);
        $this->assertResponseRedirects(self::URL_ADMIN_PLAYLISTS, "La redirection après suppression a échoué");
        $client->followRedirect();
        $this->assertResponseIsSuccessful("La page après la suppression n'est pas accessible");
        $this->assertSelectorTextContains(
            '.alert-success',
            'La playlist a bien été supprimée.',
            "Le message de succès après la suppression est incorrect"
        );
        $this->assertSelectorTextContains(
            'h3',
            "Gestion des playlists",
            "Le titre de la page après la suppression est incorrect"
        );
    }
}
