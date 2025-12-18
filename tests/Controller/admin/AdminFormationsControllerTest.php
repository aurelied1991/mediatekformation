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
        $this->assertResponseIsSuccessful("La page admin n'est pas accessible après le login");
        $this->assertSelectorTextContains('h3', 'Gestion des formations', "Le titre de la page admin est incorrect");
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
        $this->assertResponseStatusCodeSame(
            Response::HTTP_OK,
            "Le tri ASC par titre des formations a échoué (page inaccessible)"
        );
        $first = $crawler->filter('h5.text-info')->first()->text();
        $this->assertEquals(
            'Android Studio (complément n°1) : Navigation Drawer et Fragment',
            $first,
            "Le premier résultat du tri ASC par titre est incorrect"
        );
    }
    
    /**
     * Permet de tester le tri des formations par leur titre (ordre DESC) en testant
     * le résultat de la première ligne
     */
    public function testTriFormationsNameDesc()
    {
        $client = $this->loginAdmin();
        $crawler = $client->request('GET', '/admin/formations/tri/title/desc');
        $this->assertResponseStatusCodeSame(
            Response::HTTP_OK,
            "Le tri DESC par titre des formations a échoué (page inacessible"
        );
        $first = $crawler->filter('h5.text-info')->first()->text();
        $this->assertEquals(
            'UML : Diagramme de paquetages',
            $first,
            "Le premier résultat du tri DESC par titre est incorrect"
        );
    }
    
    /**
     * Permet de tester le tri des formations par le nom des playlists (ordre ASC) en contrôlant le
     * résultat de la première ligne
     */
    public function testTriFormationsPlaylistAsc()
    {
        $client = $this->loginAdmin();
        $crawler = $client->request('GET', '/admin/formations/tri/name/asc/playlist');
        $this->assertResponseStatusCodeSame(
            Response::HTTP_OK,
            "Le tri ASC par nom de playlist a échoué (page inaccessible)"
        );
        $first = $crawler->filter('h5.text-info')->first()->text();
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
        $client = $this->loginAdmin();
        $crawler = $client->request('GET', '/admin/formations/tri/name/desc/playlist');
        $this->assertResponseStatusCodeSame(
            Response::HTTP_OK,
            "Le tri DESC par nom de playlist a échoué (page inaccessible)"
        );
        $first = $crawler->filter('h5.text-info')->first()->text();
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
        $client = $this->loginAdmin();
        $client->request('GET', '/admin/formations/recherche/title');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK, "La page de recherche par titre est inaccessible");
        $crawler = $client->submitForm('Filtrer', ['recherche' => 'C#'
        ]);
        $this->assertCount(
            11,
            $crawler->filter('h5'),
            "Le nombre de formations retourné par la recherche est incorrect"
        );
        $this->assertSelectorTextContains('h5', 'C#', "Aucune formation contenant 'C#' n'a été trouvée");
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
        $client = $this->loginAdmin();
        $client->request('GET', '/admin/formations/recherche/name/playlist');
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
        $client = $this->loginAdmin();
        $crawler = $client->request('POST', '/admin/formations/recherche/id/categories', ['recherche' => 3]);
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
        $client = $this->loginAdmin();
        $crawler = $client->request('GET', '/admin/formations/tri/publishedAt/asc');
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
        $client = $this->loginAdmin();
        $crawler = $client->request('GET', '/admin/formations/tri/publishedAt/desc');
        $this->assertResponseStatusCodeSame(
            Response::HTTP_OK,
            "Le tri DESC par date de publication a échoué (page inaccessible)"
        );
        $firstDate = $crawler->filter('td.text-center')->first()->text();
        $this->assertEquals('28/10/2025', $firstDate, "Le premier résultat du tri DESC par date est incorrect");
    }
    
    /**
     * Permet de tester l'accès à la page et au formulaire d'édition d'une formation
     */
    public function testBoutonEditer()
    {
        $client = $this->loginAdmin();
        $crawler = $client->request('GET', self::URL_ADMIN);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK, "La page admin est inaccessible pour l'édition");
        $editLink = $crawler->filter('a.btn-secondary')->first()->link();
        $this->assertStringContainsString(
            '/admin/formation/edit/',
            $editLink->getUri(),
            "Le lien d'édition de formation est incorrect"
        );
        $client->click($editLink);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK, "La page d'édition de formation est inaccessible");
        $this->assertSelectorExists('form[name="formation"]', "Le formulaire d'édition de formation est introuvable");
        $this->assertSelectorTextContains(
            'h2.text-center',
            "Modification d'une formation",
            "Le titre du formulaire d'édition est incorrect"
        );
    }
    
    /**
     * Permet de tester la réussite de la suppression d'une formation
     */
    public function testBoutonSupprimer()
    {
        $client = $this->loginAdmin();
        $crawler = $client->request('GET', self::URL_ADMIN);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK, "La page admin est inaccessible pour la suppression");
        $deleteLink = $crawler->filter('a.btn-danger')->first()->link();
        $this->assertStringContainsString(
            '/admin/formation/delete/',
            $deleteLink->getUri(),
            "Le lien de suppression de formation est incorrect"
        );
        $client->click($deleteLink);
        $this->assertResponseRedirects(self::URL_ADMIN, "La redirection après suppression n'a pas eu lieu");
        $client->followRedirect();
        $this->assertResponseIsSuccessful("La page après suppression n'est pas accessible");
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
        $this->assertResponseStatusCodeSame(Response::HTTP_OK, "La page admin est inaccessible");
        $link = $crawler->filter('a[href^="/admin/formations/formation"]')->first()->link();
        $crawler = $client->click($link);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK, "La page des détails de la formation est inaccessible");
        $uri = $client->getRequest()->server->get("REQUEST_URI");
        $this->assertEquals(
            '/admin/formations/formation/1',
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
