<?php


namespace App\tests\Repository;

use App\Entity\Playlist;
use App\Repository\PlaylistRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


/**
 * Tests d'intégration sur les méthodes ajoutées au PlaylistRepository
 * Vérifie l'ajout, la suppression et différentes méthodes de tri et de recherche
 * @author Aurelie Demange
 */
class PlaylistRepositoryTest extends KernelTestCase
{
    /**
     * Récupère le repository Formation depuis le Container Symfony
     * Pratique pour réutiliser le Repository dans tous les tests
     * @return PlaylistRepository
     */
    public function recupRepository(): PlaylistRepository
    {
        self::bootKernel();
        return self::getContainer()->get(PlaylistRepository::class);
    }
    
    /**
     * Création d'une playlist valide pour les tests, avec le minimum nécessaire pour les tests : le nom
     * @return Playlist
     */
    public function getPlaylist(): Playlist
    {
        return(new Playlist())
                ->setName("Playlist pour tester");
    }
    
    /**
     * Permet de tester l'ajout d'une playlist dans la base de données
     */
    public function testAdd()
    {
        $repository = $this->recupRepository();
        $playlist = $this->getPlaylist();
        $nbPlaylists = $repository->count([]);
        $repository->add($playlist);
        $this->assertEquals($nbPlaylists + 1, $repository->count([]), "Erreur lors de l'ajout");
    }
    
    /**
     * Permet de tester la suppression d'une playlist dans la base de données
     */
    public function testRemove()
    {
        $repository = $this->recupRepository();
        $playlist = $this->getPlaylist();
        $repository->add($playlist);
        $nbPlaylists = $repository->count([]);
        $repository->remove($playlist);
        $this->assertEquals($nbPlaylists - 1, $repository->count([]), "Erreur lors de l'ajout");
    }
    
    /**
     * Permet de tester le tri des playlists par leur nom (ASC et DESC)
     * et que les tris retournent bien la première formation attendue
     */
    public function testFindAllOrderByName()
    {
        $repository = $this->recupRepository();
        $playlistsTriNomASC = $repository->findAllOrderByName("ASC");
        $this->assertEquals("Bases de la programmation (C#)", $playlistsTriNomASC[0]->getName());
        $playlistsTriNomDESC = $repository->findAllOrderByName("DESC");
        $this->assertEquals("Visual Studio 2019 et C#", $playlistsTriNomDESC[0]->getName());
    }
    
    /**
     * Permet de tester la recherche de playlists contenant une valeur spécifique
     */
    public function testFindByContainValue()
    {
        $repository = $this->recupRepository();
        $playlist = $this->getPlaylist();
        $repository->add($playlist);
        $playlists = $repository->findByContainValue("name", "tester");
        $nbPlaylists = count($playlists);
        $this->assertEquals(1, $nbPlaylists);
    }
    
    /**
     * Permet de tester le tri des playlists selon le nombre de formations qu'elles contiennent
     */
    public function testFindAllOrderByNbFormations()
    {
        $repository = $this->recupRepository();
        $nbFormationsPlaylist = $repository->findAllOrderByNbFormations("DESC");
        $this->assertNotEmpty($nbFormationsPlaylist, "Aucune playlist trouvée dans la DB de test");
        for ($i = 0; $i < count($nbFormationsPlaylist) - 1; $i++) {
        $this->assertGreaterThanOrEqual(
            count($nbFormationsPlaylist[$i+1]->getFormations()),
            count($nbFormationsPlaylist[$i]->getFormations()),
            "Le tri par nombre de formations n'est pas correct"
        );
        }
    }
}
