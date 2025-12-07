<?php


namespace App\tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;


/**
 * Tests fonctionnels du contrôleur AccueilController
 * @author Aurelie Demange
 */
class AccueilControllerTest extends WebTestCase
{
    /**
     * Permet de tester l'accès à la page d'accueil
     */
    public function testAccesPage()
    {
        //Création d'un client HTTP pour simuler un navigateur
        $client = static::createClient();
        //Envoi de la requête GET vers la page d'accueil
        $client->request('GET', '/');
        //Vérification du code de réponse HTTP (qui doit correspondre à 200)
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
}
