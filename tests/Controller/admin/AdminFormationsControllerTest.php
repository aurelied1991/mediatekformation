<?php

namespace App\tests\Controller\admin;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;



/**
 * Tests fonctionnels du contrôleur AdminFormationsController
 * Contrôle l'accès aux pages, les tris et filtres sur les formations, côté adminitrateur
 * @author Aurélie Demange
 */
class AdminFormationsControllerTest extends WebTestCase
{
    private const URL_ADMIN = '/admin';
    private const TITRE1_FORMATION = 'C# : ListBox en couleur';
    
    /**
     * Permet de se connecter en tant qu'administrateur
     * @return type
     */
    private function loginAdmin()
    {
        $client = static::createClient();
        $user = self::getContainer()->get(\App\Repository\UserRepository::class)
            ->findOneBy(['username' => 'admin']);
        $client->loginUser($user);
        $client->request('GET', self::URL_ADMIN);
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h3', 'Gestion des formations');
        return $client;
    }
    
    /**
     * Permet de tester le tri des formations par leur titre (ordre ASC) en testant
     * le résultat de la première ligne
     */
    public function testTriFormationsNameAsc()
    {
        $client = $this->loginAdmin();
        $crawler = $client->request('GET', '/admin/formations/tri/title/asc');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $first = $crawler->filter('h5.text-info')->first()->text();
        $this->assertEquals('Android Studio (complément n°1) : Navigation Drawer et Fragment', $first);
    }
    
    /**
     * Permet de tester le tri des formations par leur titre (ordre DESC) en testant
     * le résultat de la première ligne
     */
    public function testTriFormationsNameDesc()
    {
        $client = $this->loginAdmin();
        $crawler = $client->request('GET', '/admin/formations/tri/title/desc');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $first = $crawler->filter('h5.text-info')->first()->text();
        $this->assertEquals('UML : Diagramme de paquetages', $first);
    }
    
    /**
     * Permet de tester le tri des formations par le nom des playlists (ordre ASC) en contrôlant le
     * résultat de la première ligne
     */
    public function testTriFormationsPlaylistAsc()
    {
        $client = $this->loginAdmin();
        $crawler = $client->request('GET', '/admin/formations/tri/name/asc/playlist');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $first = $crawler->filter('h5.text-info')->first()->text();
        $this->assertEquals('Bases de la programmation n°74 - POO : collections', $first);
    }
    
    /**
     * Permet de tester le tri des formations par le nom des playlists (ordre DESC) en contrôlant le
     * résultat de la première ligne
     */
    public function testTriFormationsPlaylistDesc()
    {
        $client = $this->loginAdmin();
        $crawler = $client->request('GET', '/admin/formations/tri/name/desc/playlist');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $first = $crawler->filter('h5.text-info')->first()->text();
        $this->assertEquals(self::TITRE1_FORMATION, $first);
    }
    
    /**
     * Permet de tester la recherche des formations par mots-clés avec le comptage du nombre
     * de lignes obtenues ainsi que le résultat de la première ligne
     */
    public function testRechercheFormation()
    {
        $client = $this->loginAdmin();
        $client->request('GET', '/admin/formations/recherche/title');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $crawler = $client->submitForm('Filtrer', ['recherche' => 'C#'
        ]);
        $this->assertCount(11, $crawler->filter('h5'));
        $this->assertSelectorTextContains('h5', 'C#');
        $first = $crawler->filter('.text-info')->first()->text();
        $this->assertEquals(self::TITRE1_FORMATION, $first);
    }
    
    /**
     * Permet de tester le tri des formations par la recherche de playlists par mots-clés avec le comptage
     * du nombre de lignes obtenues ainsi que le résultat de la première ligne
     */
    public function testRechercheParPlaylist()
    {
        $client = $this->loginAdmin();
        $client->request('GET', '/admin/formations/recherche/name/playlist');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $crawler = $client->submitForm('Filtrer', ['recherche' => 'Cours Curseurs'
        ]);
        $this->assertCount(2, $crawler->filter('h5'));
        $this->assertSelectorTextContains('h5', 'Cours Curseurs');
        $first = $crawler->filter('.text-info')->first()->text();
        $this->assertEquals('Cours Curseurs(5 à 8 / 8) : curseur historique et curseur dans le SGBDR', $first);
    }
    
    /**
     * Permet de tester tri des formations selon le filtrage par catégorie, avec le comptage du nombre de
     * lignes obtenues ainsi que le résultat de la première ligne
     */
     public function testFiltreFormationParCategorie()
     {
        $client = $this->loginAdmin();
        $crawler = $client->request('POST', '/admin/formations/recherche/id/categories', ['recherche' => 3]);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $nombreResultats = $crawler->filter('tbody tr')->count();
        $this->assertEquals(85, $nombreResultats);
        $firstTitle = $crawler->filter('td:nth-child(1)')->first()->text();
        $this->assertEquals(self::TITRE1_FORMATION, $firstTitle);
     }
    
    /**
     * Permet de tester le tri des formations par la date de publications (ordre ASC) en contrôlant le
     * résultat de la première ligne
     */
    public function testTriFormationParDateAsc()
    {
        $client = $this->loginAdmin();
        $crawler = $client->request('GET', '/admin/formations/tri/publishedAt/asc');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $firstDate = $crawler->filter('td.text-center')->first()->text();
        $this->assertEquals('25/09/2016', $firstDate);
    }
    
    /**
     * Permet de tester le tri des formations par la date de publications (ordre DESC) en contrôlant le
     * résultat de la première ligne
     */
    public function testTriFormationParDateDesc()
    {
        $client = $this->loginAdmin();
        $crawler = $client->request('GET', '/admin/formations/tri/publishedAt/desc');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $firstDate = $crawler->filter('td.text-center')->first()->text();
        $this->assertEquals('28/10/2025', $firstDate);
    }
    
    /**
     * Permet de tester l'accès à la page et au formulaire d'édition d'une formation
     */
    public function testBoutonEditer()
    {
        $client = $this->loginAdmin();
        $crawler = $client->request('GET', self::URL_ADMIN);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $editLink = $crawler->filter('a.btn-secondary')->first()->link();
        $this->assertStringContainsString('/admin/formation/edit/', $editLink->getUri());
        $client->click($editLink);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorExists('form[name="formation"]');
        $this->assertSelectorTextContains('h2.text-center', "Modification d'une formation");
    }
    
    /**
     * Permet de tester la réussite de la suppression d'une formation
     */
    public function testBoutonSupprimer()
    {
        $client = $this->loginAdmin();
        $crawler = $client->request('GET', self::URL_ADMIN);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $deleteLink = $crawler->filter('a.btn-danger')->first()->link();
        $this->assertStringContainsString('/admin/formation/delete/', $deleteLink->getUri());
        $client->click($deleteLink);
        $this->assertResponseRedirects(self::URL_ADMIN);
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.alert-success', 'La formation a bien été supprimée');
    }
    
    /**
     * Permet de tester l'accès à la page de détails d'une formation lors du clic sur la miniature
     * ainsi que le titre de la cette page
     */
    public function testAccesDetailsFormation()
    {
        $client = $this->loginAdmin();
        $crawler = $client->request('GET', self::URL_ADMIN);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $link = $crawler->filter('a[href^="/admin/formations/formation"]')->first()->link();
        $crawler = $client->click($link);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $uri = $client->getRequest()->server->get("REQUEST_URI");
        $this->assertEquals('/admin/formations/formation/1', $uri);
        $titre = $crawler->filter('h4.text-info')->text();
        $this->assertEquals('Eclipse n°8 : Déploiement', $titre);
    }
}
