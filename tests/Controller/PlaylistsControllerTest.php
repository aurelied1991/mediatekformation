<?php

namespace App\tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;



/**
 * Tests fonctionnels du contrôleur PlaylistsController
 * Contrôle l'accès aux pages, les tris et filtres sur les playlists
 * @author Aurelie Demange
 */
class PlaylistsControllerTest extends WebTestCase
{
    private const TITRE1_PLAYLIST = 'Bases de la programmation (C#)';
    private const CSS_TEXT_INFO = '.text-info';
    private const CSS_PREMIERE_COLONNE = 'td:nth-child(1) h5';
    
    /**
     * Permet de tester l'accès à la page listant l'ensemble des playlists
     */
    public function testAccesPagePlaylists()
    {
        $client = static::createClient();
        $client->request('GET', '/playlists');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK, "La page des playlists n'est pas accessible");
    }
    
    /**
     * Permet de tester le tri des playlists par leur titre (ordre ASC) en testant
     * le résultat de la première ligne
     */
    public function testTriPlaylistsNameAsc()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/playlists/tri/name/asc');
        $this->assertResponseStatusCodeSame(
            Response::HTTP_OK,
            "Le tri ASC par nom des playlists a échoué (page inaccessible)"
        );
        $first = $crawler->filter(self::CSS_TEXT_INFO)->first()->text();
        $this->assertEquals(
            self::TITRE1_PLAYLIST,
            $first,
            "Le premier résultat du tri ASC par nom des playlists est incorrect"
        );
    }
    
    /**
     * Permet de tester le tri des playlists par leur titre (ordre DESC) en testant
     * le résultat de la première ligne
     */
    public function testTriPlaylistsNameDesc()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/playlists/tri/name/desc');
        $this->assertResponseStatusCodeSame(
            Response::HTTP_OK,
            "Le tri DESC par nom des playlists a échoué (page inaccessible)"
        );
        $first = $crawler->filter(self::CSS_TEXT_INFO)->first()->text();
        $this->assertEquals(
            'Visual Studio 2019 et C#',
            $first,
            "Le premier résultat du tri DESC par nom des playlists est incorrect"
        );
    }
    
    /**
     * Permet de tester le tri des playlists par nombre de formations de chaque playlist (ordre ASC)
     * en contrôlant le nom et le nombre de formation de la playlist de la première ligne
     */
    public function testTriPlaylistsNbFormationsAsc()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/playlists/tri/nbFormations/asc');
        $this->assertResponseStatusCodeSame(
            Response::HTTP_OK,
            "Le tri par ordre ASC par nombre de formations a échoué (page inaccessible)"
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
        $client = static::createClient();
        $crawler = $client->request('GET', '/playlists/tri/nbFormations/desc');
        $this->assertResponseStatusCodeSame(
            Response::HTTP_OK,
            "Le tri par ordre DESC par nombre de formations a échoué (page inaccessible)"
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
     * Permet de tester la recherche des playlists par mots-clés avec le comptage du nombre
     * de lignes obtenues ainsi que le titre de la première ligne
     */
    public function testRecherchePlaylist()
    {
        $client = static::createClient();
        $client->request('GET', '/playlists/recherche/name');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK, "La page de recherche par nom est inaccessible");
        $crawler = $client->submitForm('Filtrer', ['recherche' => 'C#'
        ]);
        $this->assertCount(2, $crawler->filter('h5'), "Le nombre de playlists retourné par la recherche est incorrect");
        $this->assertSelectorTextContains('h5', 'C#', "Aucune playlist contenant 'C#' n'a été trouvée");
        $first = $crawler->filter(self::CSS_TEXT_INFO)->first()->text();
        $this->assertEquals(self::TITRE1_PLAYLIST, $first, "Le premier résultat de la recherche par nom est incorrect");
    }
    
    /**
     * Permet de tester tri des playlists selon le filtrage par catégorie, avec le comptage du nombre de
     * lignes obtenues ainsi que le titre de la première ligne
     */
    public function testFiltrePlaylistCategorie()
    {
        $client = static::createClient();
        $crawler = $client->request('POST', '/playlists/recherche/id/categories', ['recherche' => 3]);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK, "La page de filtrage par catégorie est inaccessible");
        $nombreResultats = $crawler->filter('tbody tr')->count();
        $expectedCount = 2;
        $this->assertEquals(
            $expectedCount,
            $nombreResultats,
            "Le nombre de playlists retourné par le filtrage par catégorie est incorrect"
        );
        $firstPlaylistName = $crawler->filter(self::CSS_PREMIERE_COLONNE)->first()->text();
        $this->assertEquals(
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
        $client = static::createClient();
        $crawler = $client->request('GET', '/playlists');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK, "La page des playlists n'est pas accessible");
        $link = $crawler->filter('a[href^="/playlists/playlist"]')->first()->link();
        $crawler = $client->click($link);
        $this->assertResponseStatusCodeSame(
            Response::HTTP_OK,
            "La page de détails de la playlist n'est pas accessible"
        );
        $uri = $client->getRequest()->server->get("REQUEST_URI");
        $this->assertEquals(
            '/playlists/playlist/13',
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
}
