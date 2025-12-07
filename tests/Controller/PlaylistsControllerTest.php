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
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
    
    /**
     * Permet de tester le tri des playlists par leur titre (ordre ASC) en testant
     * le résultat de la première ligne
     */
    public function testTriPlaylistsNameAsc()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/playlists/tri/name/asc');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $first = $crawler->filter(self::CSS_TEXT_INFO)->first()->text();
        $this->assertEquals(self::TITRE1_PLAYLIST, $first);
    }
    
    /**
     * Permet de tester le tri des playlists par leur titre (ordre DESC) en testant
     * le résultat de la première ligne
     */
    public function testTriPlaylistsNameDesc()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/playlists/tri/name/desc');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $first = $crawler->filter(self::CSS_TEXT_INFO)->first()->text();
        $this->assertEquals('Visual Studio 2019 et C#', $first);
    }
    
    /**
     * Permet de tester le tri des playlists par nombre de formations de chaque playlist (ordre ASC)
     * en contrôlant le nom et le nombre de formation de la playlist de la première ligne
     */
    public function testTriPlaylistsNbFormationsAsc()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/playlists/tri/nbFormations/asc');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $firstNumber = intval($crawler->filter('td:nth-child(2)')->first()->text());
        $this->assertEquals(0, $firstNumber);
        $firstPlaylist = $crawler->filter(self::CSS_PREMIERE_COLONNE)->first()->text();
        $this->assertEquals('playlist test', $firstPlaylist);
    }
    
    /**
     * Permet de tester le tri des playlists par nombre de formations de chaque playlist (ordre DESC)
     * en contrôlant le nom et le nombre de formation de la playlist de la première ligne
     */
    public function testTriPlaylistsNbFormationsDesc()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/playlists/tri/nbFormations/desc');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $firstNumber = intval($crawler->filter('td:nth-child(2)')->first()->text());
        $this->assertEquals(74, $firstNumber);
        $firstPlaylist = $crawler->filter(self::CSS_PREMIERE_COLONNE)->first()->text();
        $this->assertEquals(self::TITRE1_PLAYLIST, $firstPlaylist);
    }
    
    /**
     * Permet de tester la recherche des playlists par mots-clés avec le comptage du nombre
     * de lignes obtenues ainsi que le titre de la première ligne
     */
    public function testRecherchePlaylist()
    {
        $client = static::createClient();
        $client->request('GET', '/playlists/recherche/name');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $crawler = $client->submitForm('Filtrer', ['recherche' => 'C#'
        ]);
        $this->assertCount(2, $crawler->filter('h5'));
        $this->assertSelectorTextContains('h5', 'C#');
        $first = $crawler->filter(self::CSS_TEXT_INFO)->first()->text();
        $this->assertEquals(self::TITRE1_PLAYLIST, $first);
    }
    
    /**
     * Permet de tester tri des playlists selon le filtrage par catégorie, avec le comptage du nombre de
     * lignes obtenues ainsi que le titre de la première ligne
     */
    public function testFiltrePlaylistCategorie()
    {
        $client = static::createClient();
        $crawler = $client->request('POST', '/playlists/recherche/id/categories', ['recherche' => 3]);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $nombreResultats = $crawler->filter('tbody tr')->count();
        $expectedCount = 2;
        $this->assertEquals($expectedCount, $nombreResultats);
        $firstPlaylistName = $crawler->filter(self::CSS_PREMIERE_COLONNE)->first()->text();
        $this->assertEquals(self::TITRE1_PLAYLIST, $firstPlaylistName);
    }
    
    /**
     * Permet de tester l'accès à la page de détails d'une playlist lors du clic sur la bouton 'Voir détail'
     * ainsi que le titre de la cette page
     */
    public function testAccesDetailsPlaylistFormation()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/playlists');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $link = $crawler->filter('a[href^="/playlists/playlist"]')->first()->link();
        $crawler = $client->click($link);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $uri = $client->getRequest()->server->get("REQUEST_URI");
        $this->assertEquals('/playlists/playlist/13', $uri);
        $titre = $crawler->filter('h4.text-info')->text();
        $this->assertEquals(self::TITRE1_PLAYLIST, $titre);
    }
}
