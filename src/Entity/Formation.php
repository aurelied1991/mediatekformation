<?php

namespace App\Entity;

use App\Repository\FormationRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Entité représentant une formation
 */
#[ORM\Entity(repositoryClass: FormationRepository::class)]
class Formation
{
    /**
     * Début de chemin vers les images
     */
    private const CHEMIN_IMAGE = "https://i.ytimg.com/vi/";
    
    /**
     * Identifiant unique de la formation
     * @var int|null
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    
    /**
     * Date de publication de la formation
     * @var DateTimeInterface|null
     */
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Assert\LessThanOrEqual("now", message: "La date ne peut pas être postérieure à la date d'aujourd'hui")]
    private ?DateTimeInterface $publishedAt = null;

    /**
     * Titre de la formation
     * @var string|null
     */
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $title = null;

    /**
     * Description de la formation
     * @var string|null
     */
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    /**
     * Identifiant de la vidéo associée à la formation
     * @var string|null
     */
    #[ORM\Column(length: 20, nullable: true)]
    private ?string $videoId = null;

    /**
     * Playlist à laquelle appartient la formation
     * @var Playlist|null
     */
    #[ORM\ManyToOne(inversedBy: 'formations')]
    private ?Playlist $playlist = null;

    /**
     * Collection de catégories associées à cette formation
     * @var Collection<int, Categorie>
     */
    #[ORM\ManyToMany(targetEntity: Categorie::class, inversedBy: 'formations')]
    private Collection $categories;

    /**
     * Constructeur qui initialise la collection des catégories
     */
    public function __construct()
    {
        $this->categories = new ArrayCollection();
    }

    /**
     * Retourne l'identifiant de la formation
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Retourne la date de publication
     * @return DateTimeInterface|null
     */
    public function getPublishedAt(): ?DateTimeInterface
    {
        return $this->publishedAt;
    }

    /**
     * Définit la date de publication
     * @param DateTimeInterface|null $publishedAt
     * @return static
     */
    public function setPublishedAt(?DateTimeInterface $publishedAt): static
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    /**
     * Retourne la date de publication sous la forme d'une chaîne
     * @return string
     */
    public function getPublishedAtString(): string
    {
        if ($this->publishedAt == null) {
            return "";
        }
        return $this->publishedAt->format('d/m/Y');
    }
    
    /**
     * Retourne le titre de la formation
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Définit le titre de la formation
     * @param string|null $title
     * @return static
     */
    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Retourne la description de la formation
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Définit la description de la formation
     * @param string|null $description
     * @return static
     */
    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Retourne l'identifiant de la vidéo associée
     * @return string|null
     */
    public function getVideoId(): ?string
    {
        return $this->videoId;
    }

    /**
     * Définit l'identifiant de la vidéo associée
     * @param string|null $videoId
     * @return static
     */
    public function setVideoId(?string $videoId): static
    {
        $this->videoId = $videoId;

        return $this;
    }

    /**
     * Retourne l'URL de la miniature de la vidéo
     * @return string|null
     */
    public function getMiniature(): ?string
    {
        return self::CHEMIN_IMAGE.$this->videoId."/default.jpg";
    }

    /**
     * Retourne l'URL de l'image de la vidée en haute qualité
     * @return string|null
     */
    public function getPicture(): ?string
    {
        return self::CHEMIN_IMAGE.$this->videoId."/hqdefault.jpg";
    }
    
    /**
     * Retourne la playlist associée à la formation
     * @return playlist|null
     */
    public function getPlaylist(): ?playlist
    {
        return $this->playlist;
    }

    /**
     * Définit la playlist associée à la formation
     * @param Playlist|null $playlist
     * @return static
     */
    public function setPlaylist(?Playlist $playlist): static
    {
        $this->playlist = $playlist;

        return $this;
    }

    /**
     * Retourne une collection de catégories associées à la formation
     * @return Collection<int, Categorie>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    /**
     * Ajoute une catégorie à la formation
     * @param Categorie $category
     * @return static
     */
    public function addCategory(Categorie $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    /**
     * Supprime une catégorie de cette formation
     * @param Categorie $category
     * @return static
     */
    public function removeCategory(Categorie $category): static
    {
        $this->categories->removeElement($category);

        return $this;
    }
}
