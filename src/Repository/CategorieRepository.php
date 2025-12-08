<?php

namespace App\Repository;

use App\Entity\Categorie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository pour l'entité Categorie qui fournit des méthodes pour accéder aux catégories
 * @extends ServiceEntityRepository<Categorie>
 */
class CategorieRepository extends ServiceEntityRepository
{
    /**
     * Constructeur du repository
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Categorie::class);
    }

    /**
     * Ajoute une catégorie à la base de données
     * @param Categorie $entity
     * @return void
     */
    public function add(Categorie $entity): void
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    /**
     * Supprime une catégorie de la base de données
     * @param Categorie $entity
     * @return void
     */
    public function remove(Categorie $entity): void
    {
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();
    }
    
    /**
     * Retourne la liste des catégories des formations d'une playlist
     * @param type $idPlaylist
     * @return array
     */
    public function findAllForOnePlaylist($idPlaylist): array
    {
        return $this->createQueryBuilder('c')
                ->join('c.formations', 'f')
                ->join('f.playlist', 'p')
                ->where('p.id = :id')
                ->setParameter('id', $idPlaylist)
                ->orderBy('c.name', 'ASC')
                ->getQuery()
                ->getResult();
    }
    
    /**
     * Retourne toutes les catégories triées par nom
     * @return array
     */
    public function findAllSorted(): array
    {
        return $this->createQueryBuilder('c')
                    ->orderBy('c.name', 'ASC')
                    ->getQuery()
                    ->getResult();
    }
    
    /**
     * Retourne une catégorie correspondant au nom passé en paramètre
     * @param string $nom Nom de la catégorie
     * @return Categorie|null La catégorie trouvée ou null si aucune
     */
    public function findOneByName(string $nom): ?Categorie
    {
        return $this->createQueryBuilder('c')
            //LOWER = pour rendre recherche insensible à la casse par ex Test = test
            ->where('LOWER(c.name) = LOWER(:nom)')
            ->setParameter('nom', $nom)
            ->getQuery()
            //Renvoie null si aucun résultat n'est trouvé
            ->getOneOrNullResult();
    }
}
