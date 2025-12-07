<?php


namespace App\tests\Controller\admin;

use App\Entity\Categorie;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tests fonctionnels du contrôleur AdminCategoriesController
 * Contrôle la suppression de catégories côté administrateur
 * @author Aurelie Demange
 */
class AdminCategoriesControllerTest extends WebTestCase
{
    private const URL_ADMIN_CATEGORIES = '/admin/categories';
    
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
        $client->request('GET', '/admin');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h3', 'Gestion des formations');
        return $client;
    }
    
    /**
     * Permet de tester l'échec de la suppression d'une catégorie liée à au moins une formation
     */
    public function testEchecSuppressionCategorie()
    {
        $client = $this->loginAdmin();
        $crawler = $client->request('GET', self::URL_ADMIN_CATEGORIES);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $deleteLink =  $crawler->filter('a.btn-danger')->first()->link();
        $this->assertStringContainsString('/admin/categorie/delete/', $deleteLink->getUri());
        $client->click($deleteLink);
        $this->assertResponseRedirects(self::URL_ADMIN_CATEGORIES);
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.alert-danger');
        $this->assertSelectorTextContains('h3', "Gestion des catégories");
    }
    
    /**
     * Permet de tester la réussite de la suppression d'une catégorie non liée à des formations
     */
    public function testSuccesSuppressionCategorie()
    {
        $client = $this->loginAdmin();
        $categorieTest = new Categorie();
        $categorieTest->setName('_Test Aucune formation liée');
        $entityManager = self::getContainer()->get('doctrine')->getManager();
        $entityManager->persist($categorieTest);
        $entityManager->flush();
        $crawler = $client->request('GET', self::URL_ADMIN_CATEGORIES);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $deleteLink = $crawler->filter('a.btn-danger')->first()->link();
        $this->assertStringContainsString('/admin/categorie/delete/', $deleteLink->getUri());
        $client->click($deleteLink);
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.alert-success', 'La catégorie a bien été supprimée.');
        $this->assertSelectorTextContains('h3', "Gestion des catégories");
    }
}
