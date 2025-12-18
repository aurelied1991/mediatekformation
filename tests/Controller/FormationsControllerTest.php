<?php

namespace App\tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;


/**
 * Tests fonctionnels du contrôleur FormationsController
 * Contrôle l'accès aux pages, les tris et filtres sur les formations
 * @author Aurelie Demange
 */
class FormationsControllerTest extends WebTestCase
{
    private const CSS_TEXT_INFO = 'h5.text-info';
    private const TITRE1_FORMATION = 'C# : ListBox en couleur';
    
    /**
     * Permet de tester l'accès à la page listant l'ensemble des formations
     */
    public function testAccesPageFormations()
    {
        $client = static::createClient();
        $client->request('GET', '/formations');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK, "La page des formations n'est pas accessible");
    }
    
    /**
     * Permet de tester le tri des formations par leur titre (ordre ASC) en testant
     * le résultat de la première ligne
     */
    public function testTriFormationsNameAsc()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/formations/tri/title/asc');
        $this->assertResponseStatusCodeSame(
            Response::HTTP_OK,
            "Le tri ASC par titre des formations a échoué (page inaccessible)"
        );
        $first = $crawler->filter(self::CSS_TEXT_INFO)->first()->text();
        $this->assertEquals(
            'Android Studio (complément n°1) : Navigation Drawer et Fragment',
            $first,
            "Le premier résultat du tri ASC par titre des formations est incorrect"
        );
    }
    
    /**
     * Permet de tester le tri des formations par leur titre (ordre DESC) en testant
     * le résultat de la première ligne
     */
    public function testTriFormationsNameDesc()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/formations/tri/title/desc');
        $this->assertResponseStatusCodeSame(
            Response::HTTP_OK,
            "Le tri DESC par titre des formations a échoué (page inaccessible)"
        );
        $first = $crawler->filter(self::CSS_TEXT_INFO)->first()->text();
        $this->assertEquals(
            'UML : Diagramme de paquetages',
            $first,
            "Le premier résultat du tri DESC par titre des formations est incorrect"
        );
    }
    
    /**
     * Permet de tester le tri des formations par le nom des playlists (ordre ASC) en contrôlant le
     * résultat de la première ligne
     */
    public function testTriFormationsPlaylistAsc()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/formations/tri/name/asc/playlist');
        $this->assertResponseStatusCodeSame(
            Response::HTTP_OK,
            "Le tri ASC par nom de playlist a échoué (page inaccessible)"
        );
        $first = $crawler->filter(self::CSS_TEXT_INFO)->first()->text();
        $this->assertEquals(
            'Bases de la programmation n°74 - POO : collections',
            $first,
            "Le premier résultat du tri ASC par playlist est incorrect"
        );
    }

    /**
     * Permet de tester le tri des formations par le nom des playlists (ordre DESC) en contrôlant le
     * résultat de la première ligne
     */
    public function testTriFormationsPlaylistDesc()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/formations/tri/name/desc/playlist');
        $this->assertResponseStatusCodeSame(
            Response::HTTP_OK,
            "Le tri DESC par nom de playlist a échoué (page inaccessible)"
        );
        $first = $crawler->filter(self::CSS_TEXT_INFO)->first()->text();
        $this->assertEquals(
            self::TITRE1_FORMATION,
            $first,
            "Le premier résultat du tri DESC par playlist est incorrect"
        );
    }

    /**
     * Permet de tester la recherche des formations par mots-clés avec le comptage du nombre
     * de lignes obtenues ainsi que le résultat de la première ligne
     */
    public function testRechercheFormation()
    {
        $client = static::createClient();
        $client->request('GET', '/formations/recherche/title');
        $this->assertResponseStatusCodeSame(
            Response::HTTP_OK,
            "La page de recherche par titre est inaccessible"
        );
        $crawler = $client->submitForm('Filtrer', ['recherche' => 'C#'
        ]);
        $this->assertCount(
            11,
            $crawler->filter('h5'),
            "Le nombre de formations retourné par la recherche est incorrect"
        );
        $this->assertSelectorTextContains(
            'h5',
            'C#',
            "Aucune formation contenant 'C#' n'a été trouvée"
        );
        $first = $crawler->filter('.text-info')->first()->text();
        $this->assertEquals(
            self::TITRE1_FORMATION,
            $first,
            "Le premier résultat de la recherche par titre est incorrect"
        );
    }
    
    /**
     * Permet de tester le tri des formations par la recherche de playlists par mots-clés avec le comptage
     * du nombre de lignes obtenues ainsi que le résultat de la première ligne
     */
    public function testRechercheParPlaylist()
    {
        $client = static::createClient();
        $client->request('GET', '/formations/recherche/name/playlist');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK, "La page de recherche par playlist est inaccessible");
        $crawler = $client->submitForm('Filtrer', ['recherche' => 'Cours Curseurs'
        ]);
        $this->assertCount(
            2,
            $crawler->filter('h5'),
            "Le nombre de formations retourné par la recherche sur playlist est incorrect"
        );
        $this->assertSelectorTextContains(
            'h5',
            'Cours Curseurs',
            "Aucune formation correspondant à la playlist 'Cours Curseurs' n'a été trouvée"
        );
        $first = $crawler->filter('.text-info')->first()->text();
        $this->assertEquals(
            'Cours Curseurs(5 à 8 / 8) : curseur historique et curseur dans le SGBDR',
            $first,
            "Le premier résultat de la recherche par playlist est incorrect"
        );
    }
    
    /**
     * Permet de tester tri des formations selon le filtrage par catégorie, avec le comptage du nombre de
     * lignes obtenues ainsi que le résultat de la première ligne
     */
     public function testFiltreFormationParCategorie()
     {
        $client = static::createClient();
        $crawler = $client->request('POST', '/formations/recherche/id/categories', ['recherche' => 3]);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK, "La page de filtrage par catégorie est inaccessible");
        $nombreResultats = $crawler->filter('tbody tr')->count();
        $this->assertEquals(
            85,
            $nombreResultats,
            "Le nombre de formations retourné par le filtrage par catégorie est incorrect"
        );
        $firstTitle = $crawler->filter('td:nth-child(1)')->first()->text();
        $this->assertEquals(
            self::TITRE1_FORMATION,
            $firstTitle,
            "Le premier résultat du filtrage par catégorie est incorrect"
        );
     }
     
     /**
      * Permet de tester le tri des formations par la date de publications (ordre ASC) en contrôlant le
      * résultat de la première ligne
      */
     public function testTriFormationParDateAsc()
     {
        $client = static::createClient();
        $crawler = $client->request('GET', '/formations/tri/publishedAt/asc');
        $this->assertResponseStatusCodeSame(
            Response::HTTP_OK,
            "Le tri ASC par date de publication a échoué (page inaccessible)"
        );
        $firstDate = $crawler->filter('td.text-center')->first()->text();
        $this->assertEquals('25/09/2016', $firstDate, "Le premier résultat du tri ASC par date est incorrect");
     }
     
     /**
      * Permet de tester le tri des formations par la date de publications (ordre DESC) en contrôlant le
      * résultat de la première ligne
      */
     public function testTriFormationParDateDesc()
     {
        $client = static::createClient();
        $crawler = $client->request('GET', '/formations/tri/publishedAt/desc');
        $this->assertResponseStatusCodeSame(
            Response::HTTP_OK,
            "Le tri DESC par date de publication a échoué (page inaccessible)"
        );
        $firstDate = $crawler->filter('td.text-center')->first()->text();
        $this->assertEquals('28/10/2025', $firstDate, "Le premier résultat du tri DESC par date est incorrect");
     }
    
     /**
      * Permet de tester l'accès à la page de détails d'une formation lors du clic sur la miniature
      * ainsi que le titre de la cette page
      */
    public function testAccesDetailsFormation()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/formations');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK, "La page des formations n'est pas accessible");
        $link = $crawler->filter('a[href^="/formations/formation"]')->first()->link();
        $crawler = $client->click($link);
        $response = $client->getResponse();
        $this->assertEquals(
            Response::HTTP_OK,
            $response->getStatusCode(),
            "La page de détails de la formation n'est pas accessible"
        );
        $uri = $client->getRequest()->server->get("REQUEST_URI");
        $this->assertEquals(
            '/formations/formation/1',
            $uri,
            "L'URL de la page des détails de la formation est incorrecte"
        );
        $titre = $crawler->filter('h4.text-info')->text();
        $this->assertEquals(
            'Eclipse n°8 : Déploiement',
            $titre,
            "Le titre de la page des détails de la formation est incorrect"
        );
    }
}
