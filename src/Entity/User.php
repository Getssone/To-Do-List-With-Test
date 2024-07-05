<?php

namespace App\Entity;

use App\EntityListener\UserListener;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\EntityListeners([UserListener::class])]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'Il y a déja un compte avec cette email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 64, unique: true, nullable: false)]
    #[Assert\NotBlank(message: "Un mail est obligatoire")]
    #[Assert\Email(message: "L'e-mail {{ value }} n'est pas un e-mail valide.")]
    #[Assert\Length(max: 64)]
    private ?string $email = null;

    #[ORM\Column(length: 64, unique: true, nullable: false)]
    #[Assert\NotBlank(message: "Un pseudo est obligatoire")]
    #[Assert\NoSuspiciousCharacters]
    #[Assert\Length(max: 64)]
    private ?string $username = null;

    /**
     * @var string The hashed password
     */
    #[ORM\Column(length: 180, nullable: false)]
    #[Assert\NotBlank(message: "Un mot de passe est obligatoire")]
    #[Assert\Length(min: 12, max: 180, minMessage: "Un mot de passe avec min 12 caractère", maxMessage: "Un mot de passe avec min 180 caractère")]
    private ?string $password = null;

    //Ici on ne met pas de ORM car nous ne voulons pas que le password soi envoyé en BDD
    // #[Assert\NotBlank(message: "Un mot de passe est obligatoire", groups: ["registration"])]
    // #[Assert\Length(min: 12, max: 180, minMessage: "Un mot de passe avec min 12 caractères", maxMessage: "Un mot de passe avec min 180 caractères", groups: ["registration"])]
    // private ?string $plainPassword = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column(nullable: false)]
    private array $roles = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }
    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get plain password
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * Set plain password
     */
    public function setPlainPassword(?string $plainPassword): static
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }


    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }


    //enlèvement de la function getSalt() car on utilise le hasher Password
}
