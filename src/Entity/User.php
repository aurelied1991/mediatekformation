<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Entité représentant un utilisateur de l'application
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_USERNAME', fields: ['username'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * Identifiant unique de l'utilisateur
     * @var int|null
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Nom de l'utilisateur permettant l'authentification
     * @var string|null
     */
    #[ORM\Column(length: 180)]
    private ?string $username = null;

    /**
     * Rôle(s) attribué(s) à l'utilisateur
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * Mot de passe haché de l'utilisateur
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    /**
     * Retourne l'identifiant unique de l'utilisateur
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Retourne le nom de l'utilisateur
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * Définit le nom de l'utilisateur
     * @param string $username
     * @return static
     */
    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     * Identifiant visuel de l'utilisateur requis par Symfony
     * @return string
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * Retourne la liste des rôles de l'utilisateur
     * ROLE_USER est toujours ajouté par défaut
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * Définit les rôles attribués à l'utilisateur
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Retourne le mot de passe haché
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }
    
    /**
     * Définit le mot de passe haché
     * @param string $password
     * @return static
     */
    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Supprimer les données sensibles temporaires, actuellement non utilisé
     * @return void
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
