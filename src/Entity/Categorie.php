<?php

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entité représentant une catégorie
 */
#[ORM\Entity(repositoryClass: CategorieRepository::class)]
class Categorie
{
    /**
     * Identifiant unique de la catégorie
     * @var int|null
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    
    /**
     * Nom de la catégorie
     * @var string|null
     */
    #[ORM\Column(length: 50, nullable: true)]
    private ?string $name = null;

    /**
     * Formations associées à la catégorie
     * @var Collection<int, Formation>
     */
    #[ORM\ManyToMany(targetEntity: Formation::class, mappedBy: 'categories')]
    private Collection $formations;

    /**
     * Constructeur qui initialise la collection de formations
     */
    public function __construct()
    {
        $this->formations = new ArrayCollection();
    }

    /**
     * Permet de retourner l'identifiant de la catégorie
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Permet de retourner le nom de la catégorie
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Définit le nom de la catégorie
     * @param string|null $name Nom à attribuer
     * @return static
     */
    public function setName(?string $name): static
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Retourne la collection des formations associées à une catégorie
     * @return Collection<int, Formation> La  collection de formation
     */
    public function getFormations(): Collection
    {
        return $this->formations;
    }

    /**
     * Ajoute une formation à la catégorie indiquée
     * @param Formation $formation La formation ajoutée
     * @return static
     */
    public function addFormation(Formation $formation): static
    {
        if (!$this->formations->contains($formation)) {
            $this->formations->add($formation);
            $formation->addCategory($this);
        }

        return $this;
    }

    /**
     * Supprime une formation de la catégorie indiquée
     * @param Formation $formation La formation à supprimer
     * @return static
     */
    public function removeFormation(Formation $formation): static
    {
        if ($this->formations->removeElement($formation)) {
            $formation->removeCategory($this);
        }

        return $this;
    }
}
