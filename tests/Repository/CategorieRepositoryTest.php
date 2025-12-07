<?php

namespace App\tests\Repository;

use App\Entity\Categorie;
use App\Entity\Formation;
use App\Entity\Playlist;
use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


/**
 *  Tests d'intégration sur les méthodes ajoutées au CategorieRepository
 *  Vérifie l'ajout, la suppression et différentes méthodes de tri et de recherche
 * @author Aurelie Demange
 */
class CategorieRepositoryTest extends KernelTestCase
{
    /**
     * Récupère le repository Categorie depuis le Container Symfony afin de le réutiliser
     * dans tous les tests
     * @return CategorieRepository
     */
    public function recupRepository(): CategorieRepository
    {
        self::bootKernel();
        return self::getContainer()->get(CategorieRepository::class);
    }
    
    /**
     * Création d'une catégorie pour les tests
     * @return Categorie
     */
    public function getCategorie(): Categorie
    {
        return (new Categorie())
                    ->setName("Symfony");
    }
    
    /**
     * Permet de tester l'ajout d'une catégorie dans la base de données
     */
    public function testAdd()
    {
        $repository = $this->recupRepository();
        $categorie = $this->getCategorie();
        $nbCategories = $repository->count([]);
        $repository->add($categorie);
        $this->assertEquals($nbCategories + 1, $repository->count([]), "Erreur lors de l'ajout");
    }
    
    /**
     * Permet de tester la suppression d'une catégorie dans la base de données
     */
    public function testRemove()
    {
        $repository = $this->recupRepository();
        $categorie = $this->getCategorie();
        $repository->add($categorie);
        $nbCategories = $repository->count([]);
        $repository->remove($categorie);
        $this->assertEquals($nbCategories - 1, $repository->count([]), "Erreur lors de la suppression");
    }
    
    /**
     * Test pour récupérer toutes les catégories d'une playlist spécifique
     */
    public function testFindAllForOnePlaylist()
    {
        $repository = $this->recupRepository();
        $playlist = new Playlist();
        $playlist->setName("Test Playlist");
        $categorie1 = $this->getCategorie();
        $repository->add($categorie1);
        $categorie2 = (new Categorie())->setName("Symfony intermédiaire");
        $repository->add($categorie2);
        $formation = new Formation();
        $formation->setTitle("Formations Symfony")->setPlaylist($playlist);
        $categorie1->addFormation($formation);
        $categorie2->addFormation($formation);
        $entityManager = self::getContainer()->get('doctrine')->getManager();
        $entityManager->persist($playlist);
        $entityManager->persist($formation);
        $entityManager->flush();
        $categories = $repository->findAllForOnePlaylist($playlist->getId());
        $this->assertCount(2, $categories, "Il devrait y avoir 2 catégories");
    }
    
    /**
     * Permet de tester le tri des catégories par nom (ASC)
     */
    public function testFindAllSorted()
    {
        $repository = $this->recupRepository();
        $categorieTriNomAsc = $repository->findAllSorted();
        $this->assertEquals("Android", $categorieTriNomAsc[0]->getName());
    }
    
    /**
     * Permet de tester la recherche d'une catégorie par son nom
     */
    public function testFindOneByName()
    {
        $repository = $this->recupRepository();
        $categorie = $this->getCategorie();
        $repository->add($categorie);
        $categorieRecherchee = $repository->findOneByName("Symfony");
        $this->assertNotNull($categorieRecherchee, "La catégorie n'a pas été trouvée");
        $this->assertEquals("Symfony", $categorieRecherchee->getName());
    }
}
