<?php

namespace App\Repository;

use App\Entity\Playlist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository pour l'entité Playlist qui fournit des méthodes pour accéder aux playlists
 * @extends ServiceEntityRepository<Playlist>
 */
class PlaylistRepository extends ServiceEntityRepository
{
    /**
     * Constructeur du repository
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Playlist::class);
    }

    /**
     * Ajoute une playlist à la base de données
     * @param Playlist $entity
     * @return void
     */
    public function add(Playlist $entity): void
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    /**
     * Supprime une playlist de la base de données
     * @param Playlist $entity
     * @return void
     */
    public function remove(Playlist $entity): void
    {
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();
    }

    /**
     * Retourne toutes les playlists triées par nom
     * @param type $ordre Ordre de tri : ASC ou DESC
     * @return array
     */
    public function findAllOrderByName($ordre): array
    {
        return $this->createQueryBuilder('p')
                ->leftjoin('p.formations', 'f')
                ->groupBy('p.id')
                ->orderBy('p.name', $ordre)
                ->getQuery()
                ->getResult();
    }

    /**
     * Retourne les playlists dont un champ contient une valeur
     * ou tous les enregistrements triés par nom ASC si la valeur est vide
     * @param type $champ Champ à rechercher
     * @param type $valeur Valeur à rechercher
     * @param type $table Nom de la table si le champ est dans une autre table
     * @return Playlist[]
     */
    public function findByContainValue($champ, $valeur, $table=""): array
    {
        if ($valeur=="") {
            return $this->findAllOrderByName('ASC');
        }
        if ($table=="") {
            return $this->createQueryBuilder('p')
                    ->leftjoin('p.formations', 'f')
                    ->where('p.'.$champ.' LIKE :valeur')
                    ->setParameter('valeur', '%'.$valeur.'%')
                    ->groupBy('p.id')
                    ->orderBy('p.name', 'ASC')
                    ->getQuery()
                    ->getResult();
        } else {
            return $this->createQueryBuilder('p')
                    ->leftjoin('p.formations', 'f')
                    ->leftjoin('f.categories', 'c')
                    ->where('c.'.$champ.' LIKE :valeur')
                    ->setParameter('valeur', '%'.$valeur.'%')
                    ->groupBy('p.id')
                    ->orderBy('p.name', 'ASC')
                    ->getQuery()
                    ->getResult();
        }
    }
    
    /**
     * Retourne toutes les playlists triées par nombre de formations incluses
     * @param type $ordre Ordre de tri : ASC OU DESC
     * @return array
     */
    public function findAllOrderByNbFormations($ordre): array
    {
        //création d'un QueryBuilder pour l'entité Playlist avec alias SQL'p'
        return $this->createQueryBuilder('p')
                //jointure de gauche entre playlist 'p' et formations 'f' : même si une playlist n'a pas
                //formation, elle sera incluse
                ->leftJoin('p.formations', 'f')
                //regrouper par ID de playlist et nécessaire pour orderBy
                ->groupBy('p.id')
                //tri les playlists en fonction du nombre de formations et selon choxi dans $tri (ASC ou DESC)
                ->orderBy('COUNT(f.id)', $ordre)
                //transforme le QueryBuilder en  Query exécutable
                ->getQuery()
                //exécute la requête et retourne tableau d'objets Playlist
                ->getResult();
    }
}
