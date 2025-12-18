<?php

namespace App\tests\Repository;

use App\Entity\Formation;
use App\Entity\Playlist;
use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


/**
 * Tests d'intégration sur les méthodes ajoutées au FormationRepository
 * Vérifie l'ajout, la suppression et différentes méthodes de tri et de recherche
 * @author Aurelie Demange
 */
class FormationRepositoryTest extends KernelTestCase
{
    /**
     * Récupère le repository Formation depuis le Container Symfony
     * Pratique pour réutiliser le Repository dans tous les tests
     * @return FormationRepository
     */
    public function recupRepository(): FormationRepository
    {
        self::bootKernel();
        return self::getContainer()->get(FormationRepository::class);
    }
    
    /**
     * Création d'un objet de type Formation avec une playlist associée pour l'utiliser dans les tests
     * @return Formation
     */
    public function getFormation(): Formation
    {
        $playlist = new Playlist();
        $playlist->setName("Test Playlist");
        // Persister la playlist pour éviter les erreurs avec Doctrine
        $entityManager = self::getContainer()->get('doctrine')->getManager();
        $entityManager->persist($playlist);
        $entityManager->flush();
        return (new Formation())
                        ->setTitle("Test Formation")
                        ->setPlaylist($playlist)
                        ->setPublishedAt(new \DateTime());
    }
    
    /**
     * Test de l'ajout d'une formation dans la base de données et contrôle que le nombre
     * total de formations augmente de 1 après l'ajout de la nouvelle formation
     */
    public function testAdd()
    {
        $repository = $this->recupRepository();
        $formation = $this->getFormation();
        $nbFormations = $repository->count([]);
        $repository->add($formation);
        $this->assertEquals($nbFormations + 1, $repository->count([]), "La formation n’a pas été ajoutée");
    }
    
    /**
     * Test de la suppression d'une formation dans la base de données et contrôle que le nombre
     * total de formations diminue de 1 après la suppression d'une formation
     */
    public function testRemove()
    {
        $repository = $this->recupRepository();
        $formation = $this->getFormation();
        $repository->add($formation);
        $nbFormations = $repository->count([]);
        $repository->remove($formation);
        $this->assertEquals($nbFormations - 1, $repository->count([]), "La formation n’a pas été supprimée");
    }
    
    /**
     * Test du tri des formations par titre
     * Vérifie que les tris ASC ET DESC retournent bien la première formation attendue
     */
    public function testFindAllOrderBy()
    {
        $repository = $this->recupRepository();
        // Tri par ordre ascendant
        $formationsAsc = $repository->findAllOrderBy("title", "ASC");
        $this->assertEquals(
            "Android Studio (complément n°1) : Navigation Drawer et Fragment",
            $formationsAsc[0]->getTitle(),
            "La première formation triée par ordre ASC n'est pas celle attendue"
        );
        // Tri par ordre descendant
        $formationsDesc = $repository->findAllOrderBy("title", "DESC");
        $this->assertEquals(
            "UML : Diagramme de paquetages",
            $formationsDesc[0]->getTitle(),
            "La première formation triée par ordre DESC n'est pas celle attendue"
        );
    }
    
    /**
     * Méthode pour tester la recherche de formations contenant une valeur spécifique
     * Vérifier que la méthode findByContainValue retourne la/les formation(s) correspondante(s) au critère
     */
    public function testFindByContainValue()
    {
        $repository = $this->recupRepository();
        $formation = $this->getFormation();
        $formation->setTitle("Formation pour tester");
        $repository->add($formation, true);
        $formations = $repository->findByContainValue("title", "tester");
        $nbFormations = count($formations);
        $this->assertEquals(1, $nbFormations, "La recherche n'a pas retourné le nombre attendu de formations");
    }
    
    /**
     * Test pour récupérer les dernières formations publiées et vérifier que le nombre de
     * résultats est correct avec un ordre chronologique respecté
     */
    public function testFindAllLasted()
    {
        $repository = $this->recupRepository();
        $nb = 4;
        $formationsTriDesc = $repository->findAllLasted($nb);
        $this->assertCount($nb, $formationsTriDesc, "Le nombre de formations retourné est incorrect");
        // Boucle pour vérifier que chaque formation est plus récente que la suivante
        for ($i=0; $i<count($formationsTriDesc)-1; $i++) {
            $this->assertGreaterThanOrEqual(
                $formationsTriDesc[$i+1]->getPublishedAt(),
                $formationsTriDesc[$i]->getPublishedAt(),
                "L'ordre chronologique du plus récent au plus ancien n'est pas respecté"
            );
        }
    }
    
    /**
     * Test pour récupérer toutes les formations d'une playlist spécifique et vérifier que la
     * playlist n'est pas vide et que les dates sont triées par ordre croissant
     */
    public function testFindAllForOnePlaylist()
    {
        $repository = $this->recupRepository();
        $idPlaylist = 1;
        $formations = $repository->findAllForOnePlaylist($idPlaylist);
        $this->assertNotEmpty($formations, "La playlist ne contient aucune formation");
        // Boucle pour vérifier que les dates sont triées par ordre croissant
        for ($i = 0; $i < count($formations) - 1; $i++) {
            $this->assertLessThanOrEqual(
                $formations[$i + 1]->getPublishedAt(),
                $formations[$i]->getPublishedAt(),
                "Les formations ne sont pas triées par date croissante"
            );
        }
    }
}
